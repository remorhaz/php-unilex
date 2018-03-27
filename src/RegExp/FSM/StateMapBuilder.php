<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\AST\AbstractTranslatorListener;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\RegExp\AST\NodeType;
use Remorhaz\UniLex\Stack\PushInterface;

class StateMapBuilder extends AbstractTranslatorListener
{

    private $stateMap;

    public function __construct(StateMap $stateMap)
    {
        $this->stateMap = $stateMap;
    }

    /**
     * @param Node $node
     * @throws Exception
     */
    public function onStart(Node $node): void
    {
        $stateIn = $this->stateMap->createState();
        $this->stateMap->setStartState($stateIn);
        $node->setAttribute('i.state_in', $stateIn);
        $stateOut = $this->stateMap->createState();
        $node->setAttribute('i.state_out', $stateOut);
    }

    /**
     * @param Node $node
     * @param PushInterface $stack
     * @throws Exception
     */
    public function onBeginProduction(Node $node, PushInterface $stack): void
    {
        switch ($node->getName()) {
            case NodeType::EMPTY:
            case NodeType::SYMBOL:
            case NodeType::SYMBOL_ANY:
                if (!empty($node->getChildList())) {
                    throw new Exception("AST node '{$node->getName()}' should not have child nodes");
                }
                break;

            case NodeType::ALTERNATIVE:
                if (!empty($node->getChildList())) {
                    throw new Exception("AST node '{$node->getName()}' should have child nodes");
                }
                $stack->push(...$node->getChildList());
                break;

            default:
                throw new Exception("Unknown AST node name: {$node->getName()}");
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
                $inState = $node->getAttribute('i.state_in');
                $outState = $node->getAttribute('i.state_out');
                $this->stateMap->addCharTransition($inState, $outState, $node->getAttribute('code'));
                break;

            case NodeType::EMPTY:
                $inState = $node->getAttribute('i.state_in');
                $outState = $node->getAttribute('i.state_out');
                $this->stateMap->addEpsilonTransition($inState, $outState);
                break;

            case NodeType::SYMBOL_ANY:
                $inState = $node->getAttribute('i.state_in');
                $outState = $node->getAttribute('i.state_out');
                $this->stateMap->addRangeTransition($inState, $outState, 0x00, 0x10FFFF);
                break;
        }
    }
}
