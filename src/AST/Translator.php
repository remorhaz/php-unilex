<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\AST;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Stack\SymbolStack;

class Translator
{
    private SymbolStack $stack;

    public function __construct(
        private readonly Tree $tree,
        private readonly TranslatorListenerInterface $listener,
    ) {
        $this->stack = new SymbolStack();
    }

    /**
     * @throws Exception
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
        $this->listener->onFinish();
    }
}
