<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
use Remorhaz\UniLex\Parser\LL1\Lookup\Table;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;
use Remorhaz\UniLex\Stack\SymbolStack;
use Remorhaz\UniLex\Stack\StackableSymbolInterface;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Lexer\TokenReaderInterface;
use Remorhaz\UniLex\Parser\LL1\Lookup\TableInterface;
use Remorhaz\UniLex\Parser\LL1\Lookup\TableBuilder;
use Throwable;

class Parser
{
    private ?TableInterface $lookupTable = null;

    private SymbolStack $symbolStack;

    private ?Token $token = null;

    private int $nextSymbolIndex = 0;

    /**
     * @var array<int, array{int, Production}>
     */
    private array $productionMap = [];

    public function __construct(
        private readonly GrammarInterface $grammar,
        private readonly TokenReaderInterface $tokenReader,
        private readonly ParserListenerInterface $listener,
    ) {
        $this->symbolStack = new SymbolStack();
    }

    /**
     * @throws Exception
     */
    public function loadLookupTable(string $fileName): void
    {
        $data = @include $fileName;
        if (false === $data) {
            throw new Exception("Failed to load lookup table from file $fileName");
        }
        $table = new Table();
        $table->importMap($data);
        $this->lookupTable = $table;
    }

    /**
     * @throws Exception
     * @throws UnexpectedTokenException
     */
    public function run(): void
    {
        $this->initRun();
        while ($this->hasSymbolInStack()) {
            $symbol = $this->popSymbol();
            if ($symbol instanceof Symbol) {
                $this->isTerminalSymbol($symbol)
                    ? $this->readSymbolToken($symbol)
                    : $this->pushMatchingProduction($symbol);
                continue;
            }
            if ($symbol instanceof Production) {
                $this->listener->onFinishProduction($symbol);
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

    private function createRootProduction(): Production
    {
        $rootSymbol = new Symbol($this->getNextSymbolIndex(), $this->grammar->getRootSymbol());
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

    private function isTerminalSymbol(Symbol $symbol): bool
    {
        return $this->grammar->isTerminal($symbol->getSymbolId());
    }

    private function previewToken(): Token
    {
        return $this->token ??= $this->tokenReader->read();
    }

    /**
     * @throws Exception
     */
    private function popSymbol(): StackableSymbolInterface
    {
        return $this->symbolStack->pop();
    }

    /**
     * @throws Exception
     */
    private function onSymbol(Symbol $symbol): void
    {
        [$symbolIndex, $production] = $this->productionMap[$symbol->getIndex()] ??
            throw new Exception("No production in map for symbol {$symbol->getIndex()}");
        $this->listener->onSymbol($symbolIndex, $production);
    }

    /**
     * @throws Exception
     */
    private function readSymbolToken(Symbol $symbol): void
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
     * @throws UnexpectedTokenException
     * @throws Exception
     */
    private function pushMatchingProduction(Symbol $symbol): void
    {
        $this->onSymbol($symbol);
        $production = $this->getMatchingProduction($symbol, $this->previewToken());
        $this->pushProduction($production);
    }

    private function pushProduction(Production $production): void
    {
        $this->symbolStack->push($production);
        foreach ($production->getSymbolList() as $symbolIndexInProduction => $symbol) {
            $this->productionMap[$symbol->getIndex()] = [$symbolIndexInProduction, $production];
        }
        $this->symbolStack->push(...$production->getSymbolList());
        $this->listener->onBeginProduction($production);
    }

    /**
     * @throws UnexpectedTokenException
     * @throws Exception
     */
    private function getMatchingProduction(Symbol $symbol, Token $token): Production
    {
        $lookupTable = $this->getLookupTable();
        try {
            $productionIndex = $lookupTable->getProductionIndex($symbol->getSymbolId(), $token->getType());
        } catch (Throwable $e) {
            $expectedTokenList = $lookupTable->getExpectedTokenList($symbol->getSymbolId());
            $error = new UnexpectedTokenError($token, $symbol, ...$expectedTokenList);
            throw new UnexpectedTokenException($error, previous: $e);
        }

        return $this->createParsedProduction($symbol, $productionIndex);
    }

    private function createParsedProduction(Symbol $symbol, $productionIndex): Production
    {
        $grammarProduction = $this
            ->grammar
            ->getProduction($symbol->getSymbolId(), $productionIndex);
        $symbolList = [];
        foreach ($grammarProduction->getSymbolList() as $symbolId) {
            $symbolList[] = new Symbol($this->getNextSymbolIndex(), $symbolId);
        }

        return new Production($symbol, $grammarProduction->getIndex(), ...$symbolList);
    }

    /**
     * @throws Exception
     */
    private function getLookupTable(): TableInterface
    {
        return $this->lookupTable ??= (new TableBuilder($this->grammar))->getTable();
    }
}
