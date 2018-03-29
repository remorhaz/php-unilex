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
            case NodeType::ESC_SIMPLE:
                throw new Exception("AST nodes of type '{$node->getName()}' are not supported yet");
                break;

            case NodeType::EMPTY:
            case NodeType::SYMBOL:
            case NodeType::SYMBOL_ANY:
            case NodeType::SYMBOL_CTL:
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
                    $innerStateIn = $this->stateMap->createState();
                    $innerStateOut = $this->stateMap->createState();
                    $this->stateMap->addEpsilonTransition($stateIn, $innerStateIn);
                    $this->stateMap->addEpsilonTransition($innerStateOut, $stateOut);
                    $this->stateMap->addEpsilonTransition($stateIn, $stateOut);
                    $this->stateMap->addEpsilonTransition($innerStateOut, $innerStateIn);
                    $symbolList[] = $this->createSymbolFromClonedNodeChild($node, $innerStateIn, $innerStateOut);
                    $stack->push(...$symbolList);
                    break;
                }
                $max = $node->getAttribute('max');
                if ($min > $max) {
                    throw new Exception(
                        "AST node '{$node->getName()}' has invalid attributes: min({$min}) > max({$max})"
                    );
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
                $headerStateIn = $node->getAttribute('state_in');
                $headerStateOut = $node->getAttribute('state_out');
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
                $inState = $node->getAttribute('state_in');
                $outState = $node->getAttribute('state_out');
                $this->stateMap->addCharTransition($inState, $outState, $node->getAttribute('code'));
                break;

            case NodeType::EMPTY:
                $inState = $node->getAttribute('state_in');
                $outState = $node->getAttribute('state_out');
                $this->stateMap->addEpsilonTransition($inState, $outState);
                break;

            case NodeType::SYMBOL_ANY:
                $inState = $node->getAttribute('state_in');
                $outState = $node->getAttribute('state_out');
                $this->stateMap->addRangeTransition($inState, $outState, 0x00, 0x10FFFF);
                break;

            case NodeType::SYMBOL_CTL:
                $inState = $node->getAttribute('state_in');
                $outState = $node->getAttribute('state_out');
                $code = $node->getAttribute('code');
                $this->stateMap->addCharTransition($inState, $outState, $this->getControlCode($code));
                break;
        }
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
