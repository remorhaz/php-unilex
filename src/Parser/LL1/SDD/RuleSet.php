<?php

namespace Remorhaz\UniLex\Parser\LL1\SDD;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\Production;
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
     * @param Production $production
     * @param int $symbolIndex
     * @param string $ruleKey
     * @param callable $rule
     * @throws Exception
     */
    public function addSymbolRule(Production $production, int $symbolIndex, string $ruleKey, callable $rule): void
    {
        $headerId = $production->getHeaderId();
        $productionIndex = $production->getIndex();
        if (isset($this->symbolRuleMap[$headerId][$productionIndex][$symbolIndex][$ruleKey])) {
            $symbolText = "{$headerId}:{$productionIndex}[{$symbolIndex}]->{$ruleKey}";
            throw new Exception("Rule for symbol {$symbolText} is already set");
        }
        $this->symbolRuleMap[$headerId][$productionIndex][$symbolIndex][$ruleKey] = $rule;
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
     * @param Production $production
     * @param int|string $ruleKey
     * @param callable $rule
     * @throws Exception
     */
    public function addProductionRule(Production $production, $ruleKey, callable $rule): void
    {
        $headerId = $production->getHeaderId();
        $productionIndex = $production->getIndex();
        if (isset($this->productionRuleMap[$headerId][$productionIndex][$ruleKey])) {
            throw new Exception("Rule for production {$headerId}:{$productionIndex}->{$ruleKey} is already set");
        }
        $this->productionRuleMap[$headerId][$productionIndex][$ruleKey] = $rule;
    }

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     * @throws Exception
     */
    public function applySymbolRules(ParsedProduction $production, int $symbolIndex): void
    {
        $context = $this->contextFactory->createSymbolContext($production, $symbolIndex);
        $ruleList = $this->getSymbolRuleList($production, $symbolIndex);
        foreach ($ruleList as $ruleKey => $rule) {
            $result = $rule($context);
            $production
                ->getSymbol($symbolIndex)
                ->setAttribute($ruleKey, $result);
        }
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
    public function applyProductionRules(ParsedProduction $production): void
    {
        $context = $this->contextFactory->createProductionContext($production);
        $ruleList = $this->getProductionRuleList($production);
        foreach ($ruleList as $ruleKey => $rule) {
            $result = $rule($context);
            if (is_string($ruleKey)) {
                $production
                    ->getHeader()
                    ->setAttribute($ruleKey, $result);
            }
        }
    }

    /**
     * @param ParsedProduction $production
     * @return callable[]
     */
    public function getProductionRuleList(ParsedProduction $production): array
    {
        $headerId = $production->getHeader()->getSymbolId();
        $productionIndex = $production->getIndex();
        return $this->productionRuleMap[$headerId][$productionIndex] ?? [];
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

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     * @return callable[]
     */
    private function getSymbolRuleList(ParsedProduction $production, int $symbolIndex): array
    {
        $headerId = $production->getHeader()->getSymbolId();
        $productionIndex = $production->getIndex();
        return $this->symbolRuleMap[$headerId][$productionIndex][$symbolIndex] ?? [];
    }
}
