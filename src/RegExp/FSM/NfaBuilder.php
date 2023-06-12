<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\IntRangeSets\Range;
use Remorhaz\IntRangeSets\RangeSet;
use Remorhaz\IntRangeSets\RangeSetInterface;
use Remorhaz\UCD\PropertyRangeLoader;
use Remorhaz\UCD\PropertyRangeLoaderInterface;
use Remorhaz\UniLex\AST\AbstractTranslatorListener;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\AST\Symbol;
use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\RegExp\AST\NodeType;
use Remorhaz\UniLex\RegExp\ParserFactory;
use Remorhaz\UniLex\Stack\PushInterface;

class NfaBuilder extends AbstractTranslatorListener
{
    private ?TransitionMap $rangeTransitionMap = null;

    private ?LanguageBuilder $languageBuilder = null;

    private $startState;

    public function __construct(
        private Nfa $nfa,
        private PropertyRangeLoaderInterface $propertyLoader,
    ) {
    }

    /**
     * @throws Exception
     */
    public static function fromBuffer(CharBufferInterface $buffer): Nfa
    {
        $tree = new Tree();
        ParserFactory::createFromBuffer($tree, $buffer)->run();

        return self::fromTree($tree);
    }

    /**
     * @throws Exception
     */
    public static function fromTree(Tree $tree): Nfa
    {
        $nfa = new Nfa();
        (new Translator($tree, new self($nfa, PropertyRangeLoader::create())))->run();

        return $nfa;
    }

    /**
     * @throws Exception
     */
    public function setStartState(int $state): void
    {
        $this->startState = isset($this->startState)
            ? throw new Exception("Start state is already set")
            : $state;
    }

    /**
     * @throws Exception
     */
    public function onStart(Node $node): void
    {
        $stateIn = $this->getStartState();
        $node->setAttribute('state_in', $stateIn);
        $stateOut = $this->createState();
        $this
            ->nfa
            ->getStateMap()
            ->addFinishState($stateOut);
        $node->setAttribute('state_out', $stateOut);
        $node->setAttribute('in_range', false);
    }

    /**
     * @throws Exception
     */
    private function getStartState(): int
    {
        if (!isset($this->startState)) {
            $this->startState = $this->createState();
            $this
                ->nfa
                ->getStateMap()
                ->addStartState($this->startState);
        }

        return $this->startState;
    }

    /**
     * @param Node          $node
     * @param PushInterface $stack
     * @throws Exception
     */
    public function onBeginProduction(Node $node, PushInterface $stack): void
    {
        switch ($node->getName()) {
            case NodeType::ASSERT:
                throw new Exception("AST nodes of type '{$node->getName()}' are not supported yet");

            case NodeType::EMPTY:
            case NodeType::SYMBOL:
            case NodeType::SYMBOL_ANY:
            case NodeType::SYMBOL_CTL:
            case NodeType::ESC_SIMPLE:
                if (!empty($node->getChildList())) {
                    throw new Exception("AST node '{$node->getName()}' should not have child nodes");
                }
                break;

            case NodeType::SYMBOL_PROP:
                $propName = $node->getStringAttribute('name');
                $propRangeSet = $this->propertyLoader->getRangeSet($propName);
                $rangeSet = $node->getAttribute('not')
                    ? $propRangeSet->createSymmetricDifference(
                        RangeSet::createUnsafe(new Range(0x00, 0x10FFFF))
                    )
                    : $propRangeSet;
                [$stateIn, $stateOut] = $this->getNodeStates($node);
                $this->setRangeTransition($stateIn, $stateOut, $rangeSet);
                break;

            case NodeType::SYMBOL_CLASS:
                if (empty($node->getChildList())) {
                    throw new Exception("AST node '{$node->getName()}' should have child nodes");
                }
                [$stateIn, $stateOut] = $this->getNodeStates($node);
                $symbolList = [];
                foreach ($node->getChildIndexList() as $index) {
                    $symbolList[] = $this->createSymbolFromNodeChild($node, $stateIn, $stateOut, $index);
                }
                $stack->push(...$symbolList);
                break;

            case NodeType::SYMBOL_RANGE:
                if (count($node->getChildList()) != 2) {
                    throw new Exception("AST node '{$node->getName()}' should have exactly two child nodes");
                }
                [$stateIn, $stateOut] = $this->getNodeStates($node);
                $symbolList = [];
                foreach ($node->getChildIndexList() as $index) {
                    $symbolList[] = $this->createSymbolFromNodeChild($node, $stateIn, $stateOut, $index);
                }
                $stack->push(...$symbolList);
                break;

            case NodeType::REPEAT:
                if (count($node->getChildList()) != 1) {
                    throw new Exception("AST node '{$node->getName()}' should have exactly one child node");
                }
                $min = $node->getAttribute('min');
                $symbolList = [];
                $stateOut = null;
                // Prefix concatenation construction
                for ($index = 0; $index < $min; $index++) {
                    $stateIn = $stateOut ?? $node->getIntAttribute('state_in');
                    $isLastNode =
                        !$node->getAttribute('is_max_infinite') &&
                        $index + 1 == $node->getAttribute('max');
                    $stateOut = $isLastNode
                        ? $node->getIntAttribute('state_out')
                        : $this->createState();
                    $symbolList[] = $this->createSymbolFromClonedNodeChild($node, $stateIn, $stateOut);
                }
                if ($node->getAttribute('is_max_infinite')) {
                    // Postfix Kleene star construction
                    $stateIn = $stateOut ?? $node->getAttribute('state_in');
                    $stateOut = $node->getAttribute('state_out');
                    $symbolList[] = $this->createKleeneStarSymbolFromNode($node, $stateIn, $stateOut);
                    $stack->push(...$symbolList);
                    break;
                }
                $max = $node->getAttribute('max');
                if ($min > $max) {
                    $message = "AST node '{$node->getName()}' has invalid attributes: min({$min}) > max({$max})";
                    throw new Exception($message);
                }
                // Postfix optional concatenation construction
                for ($index = $min; $index < $max; $index++) {
                    $stateIn = $stateOut ?? $node->getAttribute('state_in');
                    $stateOut = $index == $max - 1
                        ? $node->getAttribute('state_out')
                        : $this->createState();
                    $optStateOut = $node->getAttribute('state_out');
                    $this->addEpsilonTransition($stateIn, $optStateOut);
                    $symbolList[] = $this->createSymbolFromClonedNodeChild($node, $stateIn, $stateOut);
                }
                $stack->push(...$symbolList);
                break;

            case NodeType::CONCATENATE:
                if (empty($node->getChildList())) {
                    throw new Exception("AST node '{$node->getName()}' should have child nodes");
                }
                $symbolList = [];
                $stateOut = null;
                $maxIndex = count($node->getChildList()) - 1;
                foreach ($node->getChildIndexList() as $index) {
                    $stateIn = $stateOut ?? $node->getIntAttribute('state_in');
                    $stateOut = $index == $maxIndex
                        ? $node->getIntAttribute('state_out')
                        : $this->createState();
                    $symbolList[] = $this->createSymbolFromNodeChild($node, $stateIn, $stateOut, $index);
                }
                $stack->push(...$symbolList);
                break;

            case NodeType::ALTERNATIVE:
                if (empty($node->getChildList())) {
                    throw new Exception("AST node '{$node->getName()}' should have child nodes");
                }
                $symbolList = [];
                [$headerStateIn, $headerStateOut] = $this->getNodeStates($node);
                foreach ($node->getChildIndexList() as $index) {
                    $stateIn = $this->createState();
                    $stateOut = $this->createState();
                    $this->addEpsilonTransition($headerStateIn, $stateIn);
                    $this->addEpsilonTransition($stateOut, $headerStateOut);
                    $symbolList[] = $this->createSymbolFromNodeChild($node, $stateIn, $stateOut, $index);
                }
                $stack->push(...$symbolList);
                break;

            default:
                throw new Exception("Unknown AST node name: {$node->getName()}");
        }
    }

    /**
     * @throws Exception
     */
    public function onSymbol(Symbol $symbol, PushInterface $stack): void
    {
        $inRange = $symbol->getHeader()->getName() == NodeType::SYMBOL_RANGE
            ? true
            : $symbol->getHeader()->getAttribute('in_range');
        $symbol
            ->getSymbol()
            ->setAttribute('in_range', $inRange);
        $stack->push($symbol->getSymbol());
    }

    /**
     * @throws Exception
     */
    public function onFinishProduction(Node $node): void
    {
        [$stateIn, $stateOut] = $this->getNodeStates($node);
        $inRange = $node->getAttribute('in_range');
        switch ($node->getName()) {
            case NodeType::SYMBOL_RANGE:
                $startChar = $node->getChild(0)->getAttribute('range_code');
                $finishChar = $node->getChild(1)->getAttribute('range_code');
                if ($startChar > $finishChar) {
                    throw new Exception("Invalid range: start char is greater than finish char");
                }
                $this->addRangeTransition($stateIn, $stateOut, $startChar, $finishChar);
                break;

            case NodeType::SYMBOL:
                $code = $node->getAttribute('code');
                $inRange
                    ? $node->setAttribute('range_code', $code)
                    : $this->addRangeTransition($stateIn, $stateOut, $code);
                break;

            case NodeType::EMPTY:
                if ($inRange) {
                    throw new Exception("Invalid range component: no matching chars");
                }
                $this->addEpsilonTransition($stateIn, $stateOut);
                break;

            case NodeType::SYMBOL_ANY:
                if ($inRange) {
                    throw new Exception("Invalid range component: any char is matching");
                }
                $this->addRangeTransition($stateIn, $stateOut, 0x00, 0x10FFFF);
                break;

            case NodeType::SYMBOL_CTL:
                $code = $node->getAttribute('code');
                $controlCode = $this->getControlCode($code);
                $inRange
                    ? $node->setAttribute('range_code', $controlCode)
                    : $this->addRangeTransition($stateIn, $stateOut, $controlCode);
                break;

            case NodeType::ESC_SIMPLE:
                $code = $node->getAttribute('code');
                $singleCharMap = [
                    0x61 => 0x07, // \a: alert/BEL
                    0x65 => 0x1B, // \e: escape/ESC
                    0x66 => 0x0C, // \f: form feed/FF
                    0x6E => 0x0A, // \n: line feed/LF
                    0x72 => 0x0D, // \r: carriage return/CR
                    0x74 => 0x09, // \t: tab/HT
                ];
                if (isset($singleCharMap[$code])) {
                    $escapedCode = $singleCharMap[$code];
                    $inRange
                        ? $node->setAttribute('range_code', $escapedCode)
                        : $this->addRangeTransition($stateIn, $stateOut, $escapedCode);
                    break;
                }
                $notImplementedMap = [
                    0x41 => "Assertion \\A (subject start)",
                    0x42 => "Assertion \\B (not a word boundary)",
                    0x43 => "Escape \\C (single code unit)",
                    0x44 => "Escape \\D (not a decimal digit)",
                    0x45 => "Escape \\E (raw sequence end)",
                    0x47 => "Assert \\G (first matching position)",
                    0x48 => "Escape \\H (not a horizontal whitespace)",
                    0x4B => "Escape \\K (reset matching start)",
                    0x4E => "Escape \\N (not a newline)",
                    0x51 => "Escape \\Q (raw sequence start)",
                    0x52 => "Escape \\R (newline)",
                    0x53 => "Escape \\S (not a whitespace)",
                    0x56 => "Escape \\V (not a vertical whitespace)",
                    0x57 => "Escape \\W (not a \"word\" character)",
                    0x58 => "Escape \\X (Unicode extended grapheme cluster)",
                    0x5A => "Assertion \\Z (subject end or newline before subject end)",
                    0x62 => "Assertion \\b (word boundary)",
                    0x64 => "Escape \\d (decimal digit)",
                    0x67 => "Escape \\g (back-reference)",
                    0x68 => "Escape \\h (horizontal whitespace)",
                    0x6B => "Escape \\k (named back-reference)",
                    0x73 => "Escape \\s (whitespace)",
                    0x76 => "Escape \\v (vertical whitespace)",
                    0x77 => "Escape \\w (\"word\" character)",
                    0x7A => "Escape \\z (subject end)",
                ];
                if (isset($notImplementedMap[$code])) {
                    throw new Exception("{$notImplementedMap[$code]} is not implemented yet");
                }
                switch ($code) {
                    default:
                        $inRange
                            ? $node->setAttribute('range_code', $code)
                            : $this->addRangeTransition($stateIn, $stateOut, $code);
                }
                break;

            case NodeType::SYMBOL_CLASS:
                if (!$node->getAttribute('not')) {
                    break;
                }
                $invertedRangeSet = $this
                    ->getRangeTransition($stateIn, $stateOut)
                    ->createSymmetricDifference(
                        RangeSet::createUnsafe(
                            new Range(0x00, 0x10FFFF)
                        )
                    );
                $this->setRangeTransition($stateIn, $stateOut, $invertedRangeSet);
                break;
        }
    }

    public function onFinish(): void
    {
        $addTransitionToLanguage = function (RangeSetInterface $rangeSet, int $stateIn, int $stateOut) {
            $this
                ->getLanguageBuilder()
                ->addTransition($stateIn, $stateOut, ...$rangeSet->getRanges());
        };
        $this
            ->getRangeTransitionMap()
            ->onEachTransition($addTransitionToLanguage);
    }

    private function getLanguageBuilder(): LanguageBuilder
    {
        return $this->languageBuilder ??= LanguageBuilder::forNfa($this->nfa);
    }

    /**
     * @param Node $node
     * @return array{int, int}
     * @throws Exception
     */
    private function getNodeStates(Node $node): array
    {
        $inState = $node->getAttribute('state_in');
        $outState = $node->getAttribute('state_out');

        return [$inState, $outState];
    }

    /**
     * @param int $code
     * @return int
     * @throws Exception
     */
    private function getControlCode(int $code): int
    {
        if ($code < 0x20 || $code > 0x7E) {
            throw new Exception("Invalid control character: {$code}");
        }

        // Lowercase ASCII letters are converted to uppercase, then bit 6 is inverted.
        return ($code < 0x61 || $code > 0x7A ? $code : $code - 0x20) ^ 0x40;
    }

    /**
     * @param Node $node
     * @param int  $stateIn
     * @param int  $stateOut
     * @return Symbol
     * @throws Exception
     */
    private function createKleeneStarSymbolFromNode(Node $node, int $stateIn, int $stateOut): Symbol
    {
        $innerStateIn = $this->createState();
        $innerStateOut = $this->createState();
        $this
            ->addEpsilonTransition($stateIn, $innerStateIn)
            ->addEpsilonTransition($innerStateOut, $stateOut)
            ->addEpsilonTransition($stateIn, $stateOut)
            ->addEpsilonTransition($innerStateOut, $innerStateIn);

        return $this->createSymbolFromClonedNodeChild($node, $innerStateIn, $innerStateOut);
    }

    /**
     * @throws Exception
     */
    private function createSymbolFromClonedNodeChild(Node $node, int $stateIn, int $stateOut, int $index = 0): Symbol
    {
        $nodeClone = $node
            ->getChild($index)
            ->getClone()
            ->setAttribute('state_in', $stateIn)
            ->setAttribute('state_out', $stateOut);
        $symbol = new Symbol($node, $index);
        $symbol->setSymbol($nodeClone);

        return $symbol;
    }

    /**
     * @throws Exception
     */
    private function createSymbolFromNodeChild(Node $node, int $stateIn, int $stateOut, int $index = 0): Symbol
    {
        $node
            ->getChild($index)
            ->setAttribute('state_in', $stateIn)
            ->setAttribute('state_out', $stateOut);

        return new Symbol($node, $index);
    }

    /**
     * @throws Exception
     */
    private function createState(): int
    {
        return $this
            ->nfa
            ->getStateMap()
            ->createState();
    }

    private function getRangeTransitionMap(): TransitionMap
    {
        return $this->rangeTransitionMap ??= new TransitionMap($this->nfa->getStateMap());
    }

    /**
     * @throws Exception
     */
    private function addRangeTransition(int $stateIn, int $stateOut, int $start, ?int $finish = null): self
    {
        $this->setRangeTransition(
            $stateIn,
            $stateOut,
            $this
                ->getRangeTransition($stateIn, $stateOut)
                ->withRanges(new Range($start, $finish))
        );

        return $this;
    }

    /**
     * @throws Exception
     */
    private function setRangeTransition(int $stateIn, int $stateOut, RangeSetInterface $rangeSet): self
    {
        $this
            ->getRangeTransitionMap()
            ->replaceTransition($stateIn, $stateOut, $rangeSet);

        return $this;
    }

    /**
     * @throws Exception
     */
    private function getRangeTransition(int $stateIn, int $stateOut): RangeSetInterface
    {
        $transitionExists = $this
            ->getRangeTransitionMap()
            ->transitionExists($stateIn, $stateOut);
        if ($transitionExists) {
            return $this
                ->getRangeTransitionMap()
                ->getTransition($stateIn, $stateOut);
        }

        $rangeSet = RangeSet::create();
        $this
            ->getRangeTransitionMap()
            ->addTransition($stateIn, $stateOut, $rangeSet);

        return $rangeSet;
    }

    /**
     * @throws Exception
     */
    private function addEpsilonTransition(int $stateIn, int $stateOut): self
    {
        $this
            ->nfa
            ->getEpsilonTransitionMap()
            ->addTransition($stateIn, $stateOut, true);

        return $this;
    }
}
