<?php

namespace Remorhaz\UniLex\LL1Parser\Lookup;

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
            $table = new Table;
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
        foreach ($this->grammar->getNonTerminalList() as $symbolId) {
            $this->checkSymbolGrammarConflicts($symbolId);
        }
    }

    /**
     * @param int $symbolId
     * @throws Exception
     */
    private function checkSymbolGrammarConflicts(int $symbolId): void
    {
        $productionList = $this->grammar->getProductionList($symbolId);
        foreach ($productionList as $alpha) {
            foreach ($productionList as $beta) {
                if ($alpha->getIndex() == $beta->getIndex()) {
                    continue;
                }
                $this->checkFirstFirstConflict($alpha, $beta);
                $this->checkFirstFollowConflict($alpha, $beta);
            }
        }
    }

    /**
     * @param Production $alpha
     * @param Production $beta
     * @throws Exception
     */
    private function checkFirstFirstConflict(Production $alpha, Production $beta): void
    {
        if ($alpha->getSymbolId() != $beta->getSymbolId()) {
            throw new Exception("Cannot check FIRST({$alpha})/FIRST({$beta}) conflict");
        }
        $firstAlpha = $this->getFirst()->getProductionTokens(...$alpha->getSymbolList());
        $firstBeta = $this->getFirst()->getProductionTokens(...$beta->getSymbolList());
        $message = "FIRST({$alpha})/FIRST({$beta}) conflict";
        $this->checkConflict($firstAlpha, $firstBeta, $message);
    }

    /**
     * @param Production $alpha
     * @param Production $beta
     * @throws Exception
     */
    private function checkFirstFollowConflict(Production $alpha, Production $beta): void
    {
        if ($alpha->getSymbolId() != $beta->getSymbolId()) {
            throw new Exception("Cannot check FIRST({$alpha})/FOLLOW({$alpha->getSymbolId()}) conflict");
        }
        if (!$this->getFirst()->productionHasEpsilon(...$beta->getSymbolList())) {
            return;
        }
        $follow = $this->getFollow()->getTokens($alpha->getSymbolId());
        $firstAlpha = $this->getFirst()->getProductionTokens(...$alpha->getSymbolList());
        $message = "FIRST({$alpha})/FOLLOW({$alpha->getSymbolId()}) conflict (ε ∈ {$beta})";
        $this->checkConflict($follow, $firstAlpha, $message);
    }

    /**
     * @param array $tokenListA
     * @param array $tokenListB
     * @param string $message
     * @throws Exception
     */
    private function checkConflict(array $tokenListA, array $tokenListB, string $message): void
    {
        $conflict = array_intersect($tokenListA, $tokenListB);
        if (!empty($conflict)) {
            $conflictText = implode(', ', $conflict);
            throw new Exception("{$message}: {$conflictText}");
        }
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
            $table->addProduction($production->getSymbolId(), $tokenId, $production->getIndex());
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
        $productionFollows = $this->getFollow()->getTokens($production->getSymbolId());
        foreach ($productionFollows as $tokenId) {
            $table->addProduction($production->getSymbolId(), $tokenId, $production->getIndex());
        }
    }
}
