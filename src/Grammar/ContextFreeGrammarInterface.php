<?php

namespace Remorhaz\UniLex\Grammar;

use Generator;

interface ContextFreeGrammarInterface
{
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
     * @return array
     */
    public function getTerminalTokenList(int $symbolId): array;

    public function getTerminalList(): array;

    public function getNonTerminalList(): array;

    /**
     * @param int $symbolId
     * @return array
     */
    public function getProductionList(int $symbolId): array;

    /**
     * @return Generator
     */
    public function getFullProductionList(): Generator;

    public function isEoiToken(int $tokenId): bool;
}