<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\AST\AbstractTranslatorListener;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\AST\Symbol;
use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\RegExp\AST\NodeType;
use Remorhaz\UniLex\Stack\PushInterface;

class NfaBuilder extends AbstractTranslatorListener
{

    private $stateMap;

    public function __construct(StateMap $stateMap)
    {
        $this->stateMap = $stateMap;
    }

    /**
     * @param Tree $tree
     * @return StateMap
     * @throws Exception
     */
    public static function fromTree(Tree $tree): StateMap
    {
        $stateMap = new StateMap;
        (new Translator($tree, new self($stateMap)))->run();
        return $stateMap;
    }

    /**
     * @param Node $node
     * @throws Exception
     */
    public function onStart(Node $node): void
    {
        $stateIn = $this->stateMap->createState();
        $this->stateMap->setStartState($stateIn);
        $node->setAttribute('state_in', $stateIn);
        $stateOut = $this->stateMap->createState();
        $node->setAttribute('state_out', $stateOut);
    }

    /**
     * @param Node $node
     * @param PushInterface $stack
     * @throws Exception
     */
    public function onBeginProduction(Node $node, PushInterface $stack): void
    {
        switch ($node->getName()) {
            case NodeType::ASSERT:
            case NodeType::SYMBOL_RANGE:
            case NodeType::SYMBOL_CLASS:
            case NodeType::SYMBOL_PROP:
                throw new Exception("AST nodes of type '{$node->getName()}' are not supported yet");
                break;

            case NodeType::EMPTY:
            case NodeType::SYMBOL:
            case NodeType::SYMBOL_ANY:
            case NodeType::SYMBOL_CTL:
            case NodeType::ESC_SIMPLE:
                if (!empty($node->getChildList())) {
                    throw new Exception("AST node '{$node->getName()}' should not have child nodes");
                }
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
                    $stateIn = $stateOut ?? $node->getAttribute('state_in');
                    $stateOut = $this->stateMap->createState();
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
                        : $this->stateMap->createState();
                    $optStateOut = $node->getAttribute('state_out');
                    $this->stateMap->addEpsilonTransition($stateIn, $optStateOut);
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
                    $stateIn = $stateOut ?? $node->getAttribute('state_in');
                    $stateOut = $index == $maxIndex
                        ? $node->getAttribute('state_out')
                        : $this->stateMap->createState();
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
                    $stateIn = $this->stateMap->createState();
                    $stateOut = $this->stateMap->createState();
                    $this->stateMap->addEpsilonTransition($headerStateIn, $stateIn);
                    $this->stateMap->addEpsilonTransition($stateOut, $headerStateOut);
                    $symbolList[] = $this->createSymbolFromNodeChild($node, $stateIn, $stateOut, $index);
                }
                $stack->push(...$symbolList);
                break;

            default:
                throw new Exception("Unknown AST node name: {$node->getName()}");
        }
    }

    /**
     * @param Symbol $symbol
     * @param PushInterface $stack
     * @throws Exception
     */
    public function onSymbol(Symbol $symbol, PushInterface $stack): void
    {
        $header = $symbol->getHeader();
        switch ($header->getName()) {
            case NodeType::ALTERNATIVE:
            case NodeType::CONCATENATE:
            case NodeType::REPEAT:
                $stack->push($symbol->getSymbol());
                break;
        }
    }

    /**
     * @param Node $node
     * @throws Exception
     */
    public function onFinishProduction(Node $node): void
    {
        switch ($node->getName()) {
            case NodeType::SYMBOL:
                [$stateIn, $stateOut] = $this->getNodeStates($node);
                $this->stateMap->addCharTransition($stateIn, $stateOut, $node->getAttribute('code'));
                break;

            case NodeType::EMPTY:
                [$stateIn, $stateOut] = $this->getNodeStates($node);
                $this->stateMap->addEpsilonTransition($stateIn, $stateOut);
                break;

            case NodeType::SYMBOL_ANY:
                [$stateIn, $stateOut] = $this->getNodeStates($node);
                $this->stateMap->addRangeTransition($stateIn, $stateOut, 0x00, 0x10FFFF);
                break;

            case NodeType::SYMBOL_CTL:
                [$stateIn, $stateOut] = $this->getNodeStates($node);
                $code = $node->getAttribute('code');
                $this->stateMap->addCharTransition($stateIn, $stateOut, $this->getControlCode($code));
                break;

            case NodeType::ESC_SIMPLE:
                [$stateIn, $stateOut] = $this->getNodeStates($node);
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
                    $this->stateMap->addCharTransition($stateIn, $stateOut, $singleCharMap[$code]);
                    break;
                }
                $notImplementedMap = [
                    0x41, // \A: a subject start assert
                    0x42, // \B: not a word boundary assert
                    0x43, // \C: single code unit
                    0x44, // \D: not a decimal digit
                    0x45, // \E: raw sequence end
                    0x47, // \G: a first matching position assert
                    0x48, // \H: not a horizontal whitespace
                    0x4B, // \K: resets matching start
                    0x4E, // \N: not a newline
                    0x51, // \Q: raw sequence start
                    0x52, // \R: a newline
                    0x53, // \S: not a whitespace
                    0x56, // \V: not a vertical whitespace
                    0x57, // \W: not a "word" character
                    0x58, // \X: Unicode extended grapheme cluster
                    0x5A, // \Z: a subject end or newline before subject end assert
                    0x62, // \b: a word boundary assert
                    0x64, // \d: a decimal digit
                    0x67, // \g: a back reference
                    0x68, // \h: a horizontal whitespace
                    0x6B, // \k: a named back reference
                    0x73, // \s: a whitespace
                    0x76, // \v: a vertical whitespace
                    0x77, // \w: a "word" character
                    0x7A, // \z: a subject end assert
                ];
                if (in_array($code, $notImplementedMap)) {
                    throw new Exception("Escaped symbol {$code} is not implemented yet");
                }
                switch ($code) {
                    default:
                        $this->stateMap->addCharTransition($stateIn, $stateOut, $code);
                }
                break;
        }
    }

    /**
     * @param Node $node
     * @return int[]
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
     * @param int $stateIn
     * @param int $stateOut
     * @return Symbol
     * @throws Exception
     */
    private function createKleeneStarSymbolFromNode(Node $node, int $stateIn, int $stateOut): Symbol
    {
        $innerStateIn = $this->stateMap->createState();
        $innerStateOut = $this->stateMap->createState();
        $this->stateMap->addEpsilonTransition($stateIn, $innerStateIn);
        $this->stateMap->addEpsilonTransition($innerStateOut, $stateOut);
        $this->stateMap->addEpsilonTransition($stateIn, $stateOut);
        $this->stateMap->addEpsilonTransition($innerStateOut, $innerStateIn);
        return $this->createSymbolFromClonedNodeChild($node, $innerStateIn, $innerStateOut);
    }

    /**
     * @param Node $node
     * @param int $stateIn
     * @param int $stateOut
     * @param int $index
     * @return Symbol
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
     * @param Node $node
     * @param int $stateIn
     * @param int $stateOut
     * @param int $index
     * @return Symbol
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
}
