<?php

namespace Remorhaz\UniLex\Grammar\ContextFree;

interface GrammarInterface
{

    public function getRootSymbol(): int;

    public function getStartSymbol(): int;

    public function getEoiSymbol(): int;

    public function getEoiToken(): int;

    /**
     * @param int $symbolId
     * @return bool
     */
    public function isTerminal(int $symbolId): bool;

    /**
     * @param int $symbolId
     * @param int $tokenId
     * @return bool
     */
    public function tokenMatchesTerminal(int $symbolId, int $tokenId): bool;

    /**
     * @param int $symbolId
     * @return int
     */
    public function getToken(int $symbolId): int;

    public function getTerminalList(): array;

    public function getNonTerminalList(): array;

    /**
     * @param int $symbolId
     * @return Production[]
     */
    public function getProductionList(int $symbolId): array;

    public function getProduction(int $symbolId, int $productionIndex): Production;

    /**
     * @return Production[]
     */
    public function getFullProductionList(): array;

    public function isEoiToken(int $tokenId): bool;
}