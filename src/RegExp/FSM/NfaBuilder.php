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
            case NodeType::EMPTY:
            case NodeType::SYMBOL:
            case NodeType::SYMBOL_ANY:
                if (!empty($node->getChildList())) {
                    throw new Exception("AST node '{$node->getName()}' should not have child nodes");
                }
                break;

            case NodeType::CONCATENATE:
            case NodeType::ALTERNATIVE:
                if (empty($node->getChildList())) {
                    throw new Exception("AST node '{$node->getName()}' should have child nodes");
                }
                $symbolList = [];
                foreach ($node->getChildIndexList() as $index) {
                    $symbolList[] = new Symbol($node, $index);
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
                $headerStateIn = $header->getAttribute('state_in');
                $headerStateOut = $header->getAttribute('state_out');
                $stateIn = $this->stateMap->createState();
                $stateOut = $this->stateMap->createState();
                $this->stateMap->addEpsilonTransition($headerStateIn, $stateIn);
                $this->stateMap->addEpsilonTransition($stateOut, $headerStateOut);
                $symbol
                    ->getSymbol()
                    ->setAttribute('state_in', $stateIn)
                    ->setAttribute('state_out', $stateOut);
                $stack->push($symbol->getSymbol());
                break;

            case NodeType::CONCATENATE:
                $stateIn = $symbol->getIndex() == 0
                    ? $header->getAttribute('state_in')
                    : $header->getChild($symbol->getIndex() - 1)->getAttribute('state_out');
                $stateOut = $symbol->getIndex() == count($header->getChildList()) - 1
                    ? $header->getAttribute('state_out')
                    : $this->stateMap->createState();
                $symbol
                    ->getSymbol()
                    ->setAttribute('state_in', $stateIn)
                    ->setAttribute('state_out', $stateOut);
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
        }
    }

    /**
     * @param Symbol $symbol
     * @param int|null $index
     * @param string $target
     * @param string|null $source
     * @return $this
     * @throws Exception
     */
    private function inheritHeaderAttribute(Symbol $symbol, ?int $index, string $target, string $source = null)
    {
        $value = $symbol
            ->getHeader()
            ->getAttribute($source ?? $target);
        $symbol
            ->getHeader()
            ->getChild($index ?? $symbol->getIndex())
            ->setAttribute($target, $value);
        return $this;
    }

    /**
     * @param Symbol $symbol
     * @param int $index
     * @param string $target
     * @param string|null $source
     * @return $this
     * @throws Exception
     */
    private function inheritSymbolAttribute(Symbol $symbol, int $index, string $target, string $source = null)
    {
        $value = $symbol
            ->getHeader()
            ->getChild($index)
            ->getAttribute($source ?? $target);
        $symbol
            ->getSymbol()
            ->setAttribute($target, $value);
        return $this;
    }
}
