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

    private $rootSymbolId;

    public function __construct(
        GrammarInterface $grammar,
        TokenReaderInterface $tokenReader,
        int $rootSymbolId,
        ParserListenerInterface $listener
    ) {
        $this->grammar = $grammar;
        $this->tokenReader = $tokenReader;
        $this->rootSymbolId = $rootSymbolId;
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
        $rootSymbol = new ParsedSymbol($this->getNextSymbolIndex(), $this->rootSymbolId);
        $this->listener->onSymbol($rootSymbol);
        $startSymbol = new ParsedSymbol($this->getNextSymbolIndex(), $this->grammar->getStartSymbol());
        $eoiSymbol = new ParsedSymbol($this->getNextSymbolIndex(), $this->grammar->getEoiSymbol());
        $rootProduction = new ParsedProduction($rootSymbol, 0, $startSymbol, $eoiSymbol);
        $this->pushProduction($rootProduction);
    }

    private function getNextSymbolIndex(): int
    {
        return $this->nextSymbolIndex++;
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
        $production = $this->getMatchingProduction($symbol, $this->previewToken());
        $this->pushProduction($production);
    }

    private function pushProduction(ParsedProduction $production): void
    {
        $this->symbolStack->push(...$production->getSymbolList());
        $this->listener->onProduction($production);
    }

    /**
     * @param ParsedSymbol $symbol
     * @param Token $token
     * @return ParsedProduction
     * @throws Exception
     */
    private function getMatchingProduction(ParsedSymbol $symbol, Token $token): ParsedProduction
    {
        $productionIndex = $this
            ->getLookupTable()
            ->getProductionIndex($symbol->getSymbolId(), $token->getType());
        $grammarProduction = $this
            ->grammar
            ->getProduction($symbol->getSymbolId(), $productionIndex);
        $symbolList = [];
        foreach ($grammarProduction->getSymbolList() as $symbolId) {
            $symbolList[] = new ParsedSymbol($this->getNextSymbolIndex(), $symbolId);
        }
        return new ParsedProduction($symbol, $grammarProduction->getIndex(), ...$symbolList);
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
