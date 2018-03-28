<?php

namespace Remorhaz\UniLex\AST;

use Remorhaz\UniLex\Stack\SymbolStack;

class Translator
{

    private $tree;

    private $listener;

    private $stack;

    public function __construct(Tree $tree, TranslatorListenerInterface $listener)
    {
        $this->tree = $tree;
        $this->listener = $listener;
        $this->stack = new SymbolStack;
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function run(): void
    {
        $this->stack->reset();
        $rootNode = $this->tree->getRootNode();
        $this->listener->onStart($rootNode);
        $this->stack->push($rootNode);
        while (!$this->stack->isEmpty()) {
            $symbol = $this->stack->pop();
            if ($symbol instanceof EopSymbol) {
                $this->listener->onFinishProduction($symbol->getNode());
            } elseif ($symbol instanceof Node) {
                $this->stack->push(new EopSymbol($symbol));
                $this->listener->onBeginProduction($symbol, $this->stack);
            } elseif ($symbol instanceof Symbol) {
                $this->listener->onSymbol($symbol, $this->stack);
            }
        }
    }
}
