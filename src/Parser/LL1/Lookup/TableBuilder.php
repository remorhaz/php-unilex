<?php

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
use Remorhaz\UniLex\Grammar\ContextFree\Production;

class TableBuilder
{

    private $grammar;

    private $first;

    private $follow;

    private $table;

    public function __construct(GrammarInterface $grammar)
    {
        $this->grammar = $grammar;
    }

    /**
     * @return Table
     * @throws Exception
     */
    public function getTable(): TableInterface
    {
        if (!isset($this->table)) {
            $this->checkGrammarConflicts();
            $table = new Table();
            $this->addProductionsFromNonTerminalMap($table);
            $this->table = $table;
        }
        return $this->table;
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
        if (!isset($this->first)) {
            $builder = new FirstBuilder($this->grammar);
            $this->first = $builder->getFirst();
        }
        return $this->first;
    }

    /**
     * @return FollowInterface
     */
    private function getFollow(): FollowInterface
    {
        if (!isset($this->follow)) {
            $builder = new FollowBuilder($this->grammar, $this->getFirst());
            $this->follow = $builder->getFollow();
        }
        return $this->follow;
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
