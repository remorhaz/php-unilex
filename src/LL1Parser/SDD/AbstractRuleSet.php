<?php

namespace Remorhaz\UniLex\LL1Parser\SDD;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\LL1Parser\ParsedProduction;
use Remorhaz\UniLex\LL1Parser\ParsedSymbol;
use Remorhaz\UniLex\LL1Parser\ParsedToken;

abstract class AbstractRuleSet
{

    /**
     * @var callable[][][]
     */
    protected $symbolRuleMap = [];

    /**
     * @var callable[]
     */
    protected $tokenRuleMap = [];

    /**
     * @param int $headerId
     * @param int $productionIndex
     * @param int $symbolIndex
     * @param callable $rule
     * @throws Exception
     */
    public function addSymbolRule(int $headerId, int $productionIndex, int $symbolIndex, callable $rule): void
    {
        if (isset($this->symbolRuleMap[$headerId][$productionIndex][$symbolIndex])) {
            $symbolText = "{$headerId}:{$productionIndex}[{$symbolIndex}]";
            throw new Exception("Rule for symbol {$symbolText} is already set");
        }
        $this->symbolRuleMap[$headerId][$productionIndex][$symbolIndex] = $rule;
    }

    /**
     * @param int $symbolId
     * @param callable $rule
     * @throws Exception
     */
    public function addTokenRule(int $symbolId, callable $rule): void
    {
        if (isset($this->tokenRuleMap[$symbolId])) {
            throw new Exception("Rule for terminal symbol {$symbolId} is already set");
        }
        $this->tokenRuleMap[$symbolId] = $rule;
    }

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     * @throws Exception
     */
    public function applySymbolRuleIfExists(ParsedProduction $production, int $symbolIndex): void
    {
        if (!$this->symbolRuleExists($production, $symbolIndex)) {
            return;
        }
        $this->applySymbolRule($production, $symbolIndex);
    }

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     * @throws Exception
     */
    public function applySymbolRule(ParsedProduction $production, int $symbolIndex): void
    {
        $rule = $this->getSymbolRule($production, $symbolIndex);
        $rule($production, $symbolIndex);
    }

    /**
     * @param ParsedSymbol $symbol
     * @param ParsedToken $token
     * @throws Exception
     */
    public function applyTokenRule(ParsedSymbol $symbol, ParsedToken $token): void
    {
        $rule = $this->getTokenRule($symbol);
        $rule($symbol, $token);
    }

    /**
     * @param ParsedSymbol $symbol
     * @param ParsedToken $token
     * @throws Exception
     */
    public function applyTokenRuleIfExists(ParsedSymbol $symbol, ParsedToken $token): void
    {
        if (!$this->tokenRuleExists($symbol)) {
            return;
        }
        $rule = $this->getTokenRule($symbol);
        $rule($symbol, $token);
    }

    /**
     * @param ParsedSymbol $symbol
     * @return callable
     * @throws Exception
     */
    protected function getTokenRule(ParsedSymbol $symbol): callable
    {
        if (!$this->tokenRuleExists($symbol)) {
            throw new Exception("No rule defined for terminal symbol {$symbol->getSymbolId()}");
        }
        $symbolId = $symbol->getSymbolId();
        return $this->tokenRuleMap[$symbolId];
    }

    private function tokenRuleExists(ParsedSymbol $symbol): bool
    {
        $symbolId = $symbol->getSymbolId();
        return isset($this->tokenRuleMap[$symbolId]);
    }

    private function symbolRuleExists(ParsedProduction $production, int $symbolIndex): bool
    {
        $headerId = $production->getHeader()->getSymbolId();
        $productionIndex = $production->getIndex();
        return isset($this->symbolRuleMap[$headerId][$productionIndex][$symbolIndex]);
    }

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     * @return callable
     * @throws Exception
     */
    protected function getSymbolRule(ParsedProduction $production, int $symbolIndex): callable
    {
        if (!$this->symbolRuleExists($production, $symbolIndex)) {
            throw new Exception("No rule defined for production symbol {$production}[{$symbolIndex}]");
        }
        $headerId = $production->getHeader()->getSymbolId();
        $productionIndex = $production->getIndex();
        return $this->symbolRuleMap[$headerId][$productionIndex][$symbolIndex];
    }
}
