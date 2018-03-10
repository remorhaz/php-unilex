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
    protected $symbolRuleMap;

    /**
     * @var callable[]
     */
    protected $tokenRuleMap;

    /**
     * @return callable[][][]
     */
    abstract protected function createSymbolRuleMap(): array;

    /**
     * @return callable[]
     */
    abstract protected function createTokenRuleMap(): array;

    /**
     * @return callable[][][]
     */
    public function getSymbolRuleMap(): array
    {
        if (!isset($this->symbolRuleMap)) {
            $this->symbolRuleMap = $this->createSymbolRuleMap();
        }
        return $this->symbolRuleMap;
    }

    /**
     * @return callable[]
     */
    public function getTokenRuleMap(): array
    {
        if (!isset($this->tokenRuleMap)) {
            $this->tokenRuleMap = $this->createTokenRuleMap();
        }
        return $this->tokenRuleMap;
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
    private function getTokenRule(ParsedSymbol $symbol): callable
    {
        if (!$this->tokenRuleExists($symbol)) {
            throw new Exception("No rule defined for terminal symbol {$symbol->getSymbolId()}");
        }
        $ruleMap = $this->getTokenRuleMap();
        $symbolId = $symbol->getSymbolId();
        return $ruleMap[$symbolId];
    }

    private function tokenRuleExists(ParsedSymbol $symbol): bool
    {
        $ruleMap = $this->getTokenRuleMap();
        $symbolId = $symbol->getSymbolId();
        return isset($ruleMap[$symbolId]);
    }

    private function symbolRuleExists(ParsedProduction $production, int $symbolIndex): bool
    {
        $ruleMap = $this->getSymbolRuleMap();
        $headerId = $production->getHeader()->getSymbolId();
        $productionIndex = $production->getIndex();
        return isset($ruleMap[$headerId][$productionIndex][$symbolIndex]);
    }

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     * @return callable
     * @throws Exception
     */
    private function getSymbolRule(ParsedProduction $production, int $symbolIndex): callable
    {
        if (!$this->symbolRuleExists($production, $symbolIndex)) {
            throw new Exception("No rule defined for production symbol {$production}[{$symbolIndex}]");
        }
        $ruleMap = $this->getSymbolRuleMap();
        $headerId = $production->getHeader()->getSymbolId();
        $productionIndex = $production->getIndex();
        return $ruleMap[$headerId][$productionIndex][$symbolIndex];
    }
}
