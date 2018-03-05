<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
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

    private $nextSymbolIndex = 0;

    public function __construct(
        GrammarInterface $grammar,
        LexemeReaderInterface $lexemeReader,
        ParserListenerInterface $listener
    ) {
        $this->grammar = $grammar;
        $this->lexemeReader = $lexemeReader;
        $this->listener = $listener;
        $this->symbolStack = new ParsedSymbolStack;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $this->initStack();
        while ($this->hasSymbolInStack()) {
            $symbol = $this->popSymbol();
            $this->isTerminalSymbol($symbol)
                ? $this->readSymbolLexeme($symbol)
                : $this->pushMatchingProduction($symbol);
        }
    }

    private function initStack(): void
    {
        $this->nextSymbolIndex = 0;
        $this->symbolStack->reset();
        $this->listener->onStart();
        $this->pushSymbolProduction(null, ...$this->getInitSymbols());
    }

    private function getNextSymbolIndex(): int
    {
        return $this->nextSymbolIndex++;
    }

    /**
     * @return ParsedSymbol[]
     */
    private function getInitSymbols(): array
    {
        return [
            new ParsedSymbol($this->getNextSymbolIndex(), $this->grammar->getStartSymbol()),
            new ParsedSymbol($this->getNextSymbolIndex(), $this->grammar->getEoiSymbol()),
        ];
    }

    private function hasSymbolInStack(): bool
    {
        return !$this->symbolStack->isEmpty();
    }

    private function isTerminalSymbol(ParsedSymbol $symbol): bool
    {
        return $this->grammar->isTerminal($symbol->getId());
    }

    private function previewLexeme(): Lexeme
    {
        if (!isset($this->lexeme)) {
            $this->lexeme = $this->lexemeReader->read();
        }
        return $this->lexeme;
    }

    /**
     * @return ParsedSymbol
     * @throws Exception
     */
    private function popSymbol(): ParsedSymbol
    {
        $symbol = $this->symbolStack->pop();
        $this->listener->onSymbol($symbol);
        return $symbol;
    }

    /**
     * @param ParsedSymbol $symbol
     * @throws Exception
     */
    private function readSymbolLexeme(ParsedSymbol $symbol): void
    {
        $lexeme = $this->previewLexeme();
        if (!$this->grammar->tokenMatchesTerminal($symbol->getId(), $lexeme->getType())) {
            throw new Exception("Unexpected token {$lexeme->getType()} for symbol {$symbol->getId()}");
        }
        $parsedLexeme = new ParsedLexeme($this->getNextSymbolIndex(), $lexeme);
        $lexeme->isEoi()
            ? $this->listener->onEoi($symbol, $parsedLexeme)
            : $this->listener->onLexeme($symbol, $parsedLexeme);
        unset($this->lexeme);
    }

    /**
     * @param ParsedSymbol $symbol
     * @throws Exception
     */
    private function pushMatchingProduction(ParsedSymbol $symbol): void
    {
        $tokenId = $this->previewLexeme()->getType();
        $production = [];
        foreach ($this->getLookupTable()->getProduction($symbol->getId(), $tokenId) as $symbolId) {
            $production[] = new ParsedSymbol($this->getNextSymbolIndex(), $symbolId);
        }
        $this->pushSymbolProduction($symbol, ...$production);
    }

    private function pushSymbolProduction(?ParsedSymbol $symbol, ParsedSymbol ...$production): void
    {
        $this->symbolStack->push(...$production);
        $this->listener->onProduction($symbol, ...$production);
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
