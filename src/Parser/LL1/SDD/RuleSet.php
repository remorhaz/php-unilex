<?php

namespace Remorhaz\UniLex\Parser\LL1\SDD;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Parser\ParsedToken;

class RuleSet
{

    /**
     * @var callable[][][]
     */
    protected $symbolRuleMap = [];

    /**
     * @var callable[]
     */
    protected $tokenRuleMap = [];

    protected $productionRuleMap = [];

    private $contextFactory;

    public function __construct(ContextFactoryInterface $contextFactory)
    {
        $this->contextFactory = $contextFactory;
    }

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
     * @param int $headerId
     * @param int $productionIndex
     * @param callable $rule
     * @throws Exception
     */
    public function addProductionRule(int $headerId, int $productionIndex, callable $rule): void
    {
        if (isset($this->productionRuleMap[$headerId][$productionIndex])) {
            throw new Exception("Rule for production {$headerId}:{$productionIndex} is already set");
        }
        $this->productionRuleMap[$headerId][$productionIndex] = $rule;
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
        $context = $this->contextFactory->createSymbolContext($production, $symbolIndex);
        $this->getSymbolRule($production, $symbolIndex)($context);
    }

    /**
     * @param ParsedSymbol $symbol
     * @param ParsedToken $token
     * @throws Exception
     */
    public function applyTokenRule(ParsedSymbol $symbol, ParsedToken $token): void
    {
        $context = $this->contextFactory->createTokenContext($symbol, $token);
        $this->getTokenRule($symbol)($context);
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
        $this->applyTokenRule($symbol, $token);
    }

    /**
     * @param ParsedProduction $production
     * @throws Exception
     */
    public function applyProductionRule(ParsedProduction $production): void
    {
        $context = $this->contextFactory->createProductionContext($production);
        $this->getProductionRule($production)($context);
    }

    /**
     * @param ParsedProduction $production
     * @throws Exception
     */
    public function applyProductionRuleIfExists(ParsedProduction $production): void
    {
        if (!$this->productionRuleExists($production)) {
            return;
        }
        $this->applyProductionRule($production);
    }

    public function productionRuleExists(ParsedProduction $production): bool
    {
        $headerId = $production->getHeader()->getSymbolId();
        $productionIndex = $production->getIndex();
        return isset($this->productionRuleMap[$headerId][$productionIndex]);
    }

    /**
     * @param ParsedProduction $production
     * @return callable
     * @throws Exception
     */
    public function getProductionRule(ParsedProduction $production): callable
    {
        $headerId = $production->getHeader()->getSymbolId();
        $productionIndex = $production->getIndex();
        if (!isset($this->productionRuleMap[$headerId][$productionIndex])) {
            throw new Exception("Production rule {$headerId}:{$productionIndex} is not defined");
        }
        return $this->productionRuleMap[$headerId][$productionIndex];
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
    private function getSymbolRule(ParsedProduction $production, int $symbolIndex): callable
    {
        if (!$this->symbolRuleExists($production, $symbolIndex)) {
            throw new Exception("No rule defined for production symbol {$production}[{$symbolIndex}]");
        }
        $headerId = $production->getHeader()->getSymbolId();
        $productionIndex = $production->getIndex();
        return $this->symbolRuleMap[$headerId][$productionIndex][$symbolIndex];
    }
}
