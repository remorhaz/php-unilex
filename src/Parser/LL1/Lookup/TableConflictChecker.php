<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
use Remorhaz\UniLex\Grammar\ContextFree\Production;

class TableConflictChecker
{
    public function __construct(
        private readonly GrammarInterface $grammar,
        private readonly FirstInterface $first,
        private readonly FollowInterface $follow,
    ) {
    }

    /**
     * @throws Exception
     */
    public function check(): void
    {
        foreach ($this->grammar->getNonTerminalList() as $symbolId) {
            $this->checkSymbolConflicts($symbolId);
        }
    }

    /**
     * @throws Exception
     */
    private function checkSymbolConflicts(int $symbolId): void
    {
        $productionList = $this->grammar->getProductionList($symbolId);
        $this->checkMultipleEpsilons($symbolId, ...$productionList);
        foreach ($productionList as $alpha) {
            foreach ($productionList as $beta) {
                $this->checkProductionConflicts($alpha, $beta);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function checkProductionConflicts(Production $alpha, Production $beta): void
    {
        if ($alpha->getIndex() == $beta->getIndex()) {
            return;
        }

        $this->checkFirstFirstConflict($alpha, $beta);
        $this->checkFirstFollowConflict($alpha, $beta);
    }

    /**
     * @throws Exception
     */
    private function checkMultipleEpsilons(int $symbolId, Production ...$productionList): void
    {
        $isEpsilonProduction = fn (Production $production): bool => $production->isEpsilon();

        $epsilonProductionList = array_filter($productionList, $isEpsilonProduction);
        if (count($epsilonProductionList) > 1) {
            throw new Exception("Symbol {$symbolId} has multiple ε-productions");
        }
    }

    /**
     * @throws Exception
     */
    private function checkFirstFirstConflict(Production $alpha, Production $beta): void
    {
        $firstAlpha = $this->first->getProductionTokens(...$alpha->getSymbolList());
        $firstBeta = $this->first->getProductionTokens(...$beta->getSymbolList());
        $message = "FIRST($alpha)/FIRST($beta) conflict";
        $this->checkConflict($firstAlpha, $firstBeta, $message);
    }

    /**
     * @throws Exception
     */
    private function checkFirstFollowConflict(Production $alpha, Production $beta): void
    {
        if (!$this->first->productionHasEpsilon(...$beta->getSymbolList())) {
            return;
        }

        $follow = $this->follow->getTokens($alpha->getHeaderId());
        $firstAlpha = $this->first->getProductionTokens(...$alpha->getSymbolList());
        $message = "FIRST($alpha)/FOLLOW({$alpha->getHeaderId()}) conflict (ε ∈ $beta)";
        $this->checkConflict($follow, $firstAlpha, $message);
    }

    /**
     * @param list<int> $tokenListA
     * @param list<int> $tokenListB
     * @param string $message
     * @throws Exception
     */
    private function checkConflict(array $tokenListA, array $tokenListB, string $message): void
    {
        $conflict = array_intersect($tokenListA, $tokenListB);
        if (!empty($conflict)) {
            $conflictText = implode(', ', $conflict);
            throw new Exception("$message: $conflictText");
        }
    }
}
