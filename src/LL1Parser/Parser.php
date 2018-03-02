<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\EoiLexeme;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFreeGrammar;
use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeReaderInterface;
use Remorhaz\UniLex\LL1Parser\Lookup\TableInterface;
use Remorhaz\UniLex\LL1Parser\Lookup\TableBuilder;

class Parser
{

    private $grammar;

    private $lookupTable;

    private $symbolStack;

    private $lexeme;

    private $lexemeReader;

    private $listener;

    public function __construct(
        ContextFreeGrammar $grammar,
        LexemeReaderInterface $lexemeReader,
        ParserListenerInterface $listener
    ) {
        $this->grammar = $grammar;
        $this->lexemeReader = $lexemeReader;
        $this->listener = $listener;
        $this->symbolStack = new SymbolStack;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $this->initStack();
        while ($this->hasSymbolInStack()) {
            $symbolId = $this->popSymbol();
            $this->isTerminalSymbol($symbolId)
                ? $this->readSymbolLexeme($symbolId)
                : $this->pushSymbolProduction($symbolId);
        }
    }

    private function initStack(): void
    {
        $this->symbolStack->reset();
        $this->symbolStack->push(...$this->getInitSymbols());
    }

    private function getInitSymbols(): array
    {
        return [$this->grammar->getStartSymbol(), $this->grammar->getEoiSymbol()];
    }

    private function hasSymbolInStack(): bool
    {
        return !$this->symbolStack->isEmpty();
    }

    private function isTerminalSymbol(int $symbolId): bool
    {
        return $this->grammar->isTerminal($symbolId);
    }

    private function previewLexeme(): Lexeme
    {
        if (!isset($this->lexeme)) {
            $this->lexeme = $this->lexemeReader->read();
        }
        return $this->lexeme;
    }

    /**
     * @return int
     * @throws Exception
     */
    private function popSymbol(): int
    {
        $symbolId = $this->symbolStack->pop();
        $lexeme = $this->previewLexeme();
        $this->listener->onSymbol($symbolId, $lexeme);
        return $symbolId;
    }

    /**
     * @param int $symbolId
     * @throws Exception
     */
    private function readSymbolLexeme(int $symbolId): void
    {
        $lexeme = $this->previewLexeme();
        if (!$this->grammar->tokenMatchesTerminal($symbolId, $lexeme->getType())) {
            throw new Exception("Unexpected token {$lexeme->getType()} for symbol {$symbolId}");
        }
        ($lexeme instanceof EoiLexeme)
            ? $this->listener->onEoi($lexeme)
            : $this->listener->onLexeme($lexeme);
        unset($this->lexeme);
    }

    /**
     * @param int $symbolId
     * @throws Exception
     */
    private function pushSymbolProduction(int $symbolId): void
    {
        $tokenId = $this->previewLexeme()->getType();
        $symbolIdList = $this->getLookupTable()->getProduction($symbolId, $tokenId);
        $this->symbolStack->push(...$symbolIdList);
    }

    /**
     * @return TableInterface
     * @throws Exception
     */
    private function getLookupTable(): TableInterface
    {
        if (!isset($this->lookupTable)) {
            $builder = new TableBuilder($this->grammar);
            $this->lookupTable = $builder->getTable();
        }
        return $this->lookupTable;
    }
}
