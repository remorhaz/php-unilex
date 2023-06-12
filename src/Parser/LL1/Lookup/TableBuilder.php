<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
use Remorhaz\UniLex\Grammar\ContextFree\Production;

class TableBuilder
{
    private ?FirstInterface $first = null;

    private ?FollowInterface $follow = null;

    private ?TableInterface $table = null;

    public function __construct(
        private GrammarInterface $grammar,
    ) {
    }

    /**
     * @throws Exception
     */
    public function getTable(): TableInterface
    {
        return $this->table ??= $this->buildTable();
    }

    /**
     * @throws Exception
     */
    private function buildTable(): TableInterface
    {
        $this->checkGrammarConflicts();
        $table = new Table();
        $this->addProductionsFromNonTerminalMap($table);

        return $table;
    }

    /**
     * @throws Exception
     */
    private function checkGrammarConflicts(): void
    {
        $checker = new TableConflictChecker($this->grammar, $this->getFirst(), $this->getFollow());
        $checker->check();
    }

    /**
     * @return FirstInterface
     */
    private function getFirst(): FirstInterface
    {
        return $this->first ??= (new FirstBuilder($this->grammar))->getFirst();
    }

    /**
     * @return FollowInterface
     */
    private function getFollow(): FollowInterface
    {
        return $this->follow ??= (new FollowBuilder($this->grammar, $this->getFirst()))->getFollow();
    }

    /**
     * @param Table $table
     * @throws Exception
     */
    private function addProductionsFromNonTerminalMap(Table $table): void
    {
        foreach ($this->grammar->getFullProductionList() as $production) {
            if ($production->getHeaderId() == $this->grammar->getRootSymbol()) {
                continue;
            }
            $this->addProductionFirsts($table, $production);
            $this->addProductionFollows($table, $production);
        }
    }

    /**
     * @param Table $table
     * @param Production $production
     * @throws Exception
     */
    private function addProductionFirsts(Table $table, Production $production): void
    {
        $productionFirsts = $this->getFirst()->getProductionTokens(...$production->getSymbolList());
        foreach ($productionFirsts as $tokenId) {
            $table->addProduction($production->getHeaderId(), $tokenId, $production->getIndex());
        }
    }

    /**
     * @param Table $table
     * @param Production $production
     * @throws Exception
     */
    private function addProductionFollows(Table $table, Production $production): void
    {
        if (!$this->getFirst()->productionHasEpsilon(...$production->getSymbolList())) {
            return;
        }
        $productionFollows = $this->getFollow()->getTokens($production->getHeaderId());
        foreach ($productionFollows as $tokenId) {
            $table->addProduction($production->getHeaderId(), $tokenId, $production->getIndex());
        }
    }
}
