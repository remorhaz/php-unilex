<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\TokenReaderInterface;
use Remorhaz\UniLex\LL1Parser\Lookup\TableInterface;
use Remorhaz\UniLex\LL1Parser\Lookup\TableBuilder;

class Parser
{

    private $grammar;

    private $lookupTable;

    private $symbolStack;

    private $token;

    private $tokenReader;

    private $listener;

    private $nextSymbolIndex = 0;

    public function __construct(
        GrammarInterface $grammar,
        TokenReaderInterface $tokenReader,
        ParserListenerInterface $listener
    ) {
        $this->grammar = $grammar;
        $this->tokenReader = $tokenReader;
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
                ? $this->readSymbolToken($symbol)
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
            new ParsedSymbol($this->getNextSymbolIndex(), $this->grammar->getStartSymbol(), 0),
            new ParsedSymbol($this->getNextSymbolIndex(), $this->grammar->getEoiSymbol(), 0),
        ];
    }

    private function hasSymbolInStack(): bool
    {
        return !$this->symbolStack->isEmpty();
    }

    private function isTerminalSymbol(ParsedSymbol $symbol): bool
    {
        return $this->grammar->isTerminal($symbol->getSymbolId());
    }

    private function previewToken(): Token
    {
        if (!isset($this->token)) {
            $this->token = $this->tokenReader->read();
        }
        return $this->token;
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
    private function readSymbolToken(ParsedSymbol $symbol): void
    {
        $token = $this->previewToken();
        if (!$this->grammar->tokenMatchesTerminal($symbol->getSymbolId(), $token->getType())) {
            throw new Exception("Unexpected token {$token->getType()} for symbol {$symbol->getSymbolId()}");
        }
        $parsedToken = new ParsedToken($this->getNextSymbolIndex(), $token);
        $token->isEoi()
            ? $this->listener->onEoi($symbol, $parsedToken)
            : $this->listener->onToken($symbol, $parsedToken);
        unset($this->token);
    }

    /**
     * @param ParsedSymbol $symbol
     * @throws Exception
     */
    private function pushMatchingProduction(ParsedSymbol $symbol): void
    {
        $tokenId = $this->previewToken()->getType();
        $production = [];
        $productionIndex = $this->getLookupTable()->getProductionIndex($symbol->getSymbolId(), $tokenId);
        foreach ($this->grammar->getProduction($symbol->getSymbolId(), $productionIndex) as $symbolId) {
            $production[] = new ParsedSymbol($this->getNextSymbolIndex(), $symbolId, $productionIndex);
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
