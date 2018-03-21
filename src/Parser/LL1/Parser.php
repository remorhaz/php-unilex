<?php

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
use Remorhaz\UniLex\Parser\EopSymbol;
use Remorhaz\UniLex\Parser\LL1\Lookup\Table;
use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Stack\SymbolStack;
use Remorhaz\UniLex\Stack\StackableSymbolInterface;
use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\TokenReaderInterface;
use Remorhaz\UniLex\Parser\LL1\Lookup\TableInterface;
use Remorhaz\UniLex\Parser\LL1\Lookup\TableBuilder;

class Parser
{

    private $grammar;

    private $lookupTable;

    private $symbolStack;

    private $token;

    private $tokenReader;

    private $listener;

    private $nextSymbolIndex = 0;

    /**
     * @var ParsedProduction[]
     */
    private $productionMap = [];

    public function __construct(
        GrammarInterface $grammar,
        TokenReaderInterface $tokenReader,
        ParserListenerInterface $listener
    ) {
        $this->grammar = $grammar;
        $this->tokenReader = $tokenReader;
        $this->listener = $listener;
        $this->symbolStack = new SymbolStack;
    }

    /**
     * @param string $fileName
     * @throws Exception
     */
    public function loadLookupTable(string $fileName): void
    {
        /** @noinspection PhpIncludeInspection */
        $data = @include $fileName;
        if (false === $data) {
            throw new Exception("Failed to load lookup table from file {$fileName}");
        }
        $table = new Table;
        $table->importMap($data);
        $this->lookupTable = $table;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $this->initRun();
        while ($this->hasSymbolInStack()) {
            $symbol = $this->popSymbol();
            if ($symbol instanceof ParsedSymbol) {
                $this->isTerminalSymbol($symbol)
                    ? $this->readSymbolToken($symbol)
                    : $this->pushMatchingProduction($symbol);
                continue;
            }
            if ($symbol instanceof EopSymbol) {
                $this->listener->onFinishProduction($symbol->getProduction());
            }
        }
    }

    private function initRun(): void
    {
        $this->nextSymbolIndex = 0;
        $this->productionMap = [];
        $this->symbolStack->reset();
        $this->listener->onStart();
        $this->pushProduction($this->createRootProduction());
    }

    private function createRootProduction(): ParsedProduction
    {
        $rootSymbol = new ParsedSymbol($this->getNextSymbolIndex(), $this->grammar->getRootSymbol());
        $this->listener->onRootSymbol($rootSymbol);
        return $this->createParsedProduction($rootSymbol, 0);
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
     * @return StackableSymbolInterface
     * @throws Exception
     */
    private function popSymbol(): StackableSymbolInterface
    {
        return $this->symbolStack->pop();
    }

    /**
     * @param ParsedSymbol $symbol
     * @throws Exception
     */
    private function onSymbol(ParsedSymbol $symbol): void
    {
        if (!isset($this->productionMap[$symbol->getIndex()])) {
            throw new Exception("No production in map for symbol {$symbol->getIndex()}");
        }
        [$symbolIndex, $production] = $this->productionMap[$symbol->getIndex()];
        $this->listener->onSymbol($symbolIndex, $production);
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
        $token->isEoi()
            ? $this->listener->onEoi($symbol, $token)
            : $this->listener->onToken($symbol, $token);
        $this->onSymbol($symbol);
        unset($this->token);
    }

    /**
     * @param ParsedSymbol $symbol
     * @throws Exception
     */
    private function pushMatchingProduction(ParsedSymbol $symbol): void
    {
        $this->onSymbol($symbol);
        $production = $this->getMatchingProduction($symbol, $this->previewToken());
        $this->pushProduction($production);
    }

    private function pushProduction(ParsedProduction $production): void
    {
        $this->symbolStack->push(new EopSymbol($production));
        foreach ($production->getSymbolList() as $symbolIndexInProduction => $symbol) {
            $this->productionMap[$symbol->getIndex()] = [$symbolIndexInProduction, $production];
        }
        $this->symbolStack->push(...$production->getSymbolList());
        $this->listener->onBeginProduction($production);
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
        return $this->createParsedProduction($symbol, $productionIndex);
    }

    private function createParsedProduction(ParsedSymbol $symbol, $productionIndex): ParsedProduction
    {
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
