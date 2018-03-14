<?php

namespace Remorhaz\UniLex\Grammar\SDD;

use Closure;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\Production;
use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Parser\ParsedToken;

class TranslationScheme
{

    /**
     * @var callable[][][]
     */
    protected $symbolActionMap = [];

    /**
     * @var callable[]
     */
    protected $tokenActionMap = [];

    protected $productionActionMap = [];

    private $contextFactory;

    public function __construct(ContextFactoryInterface $contextFactory)
    {
        $this->contextFactory = $contextFactory;
    }

    /**
     * @param Production $production
     * @param int $symbolIndex
     * @param string $attribute
     * @param Closure $action
     * @throws Exception
     */
    public function addSymbolAction(Production $production, int $symbolIndex, string $attribute, Closure $action): void
    {
        $headerId = $production->getHeaderId();
        $productionIndex = $production->getIndex();
        if (isset($this->symbolActionMap[$headerId][$productionIndex][$symbolIndex][$attribute])) {
            $symbolText = "{$headerId}:{$productionIndex}[{$symbolIndex}]->{$attribute}";
            throw new Exception("Action for symbol {$symbolText} is already set");
        }
        $this->symbolActionMap[$headerId][$productionIndex][$symbolIndex][$attribute] = $action;
    }

    /**
     * @param int $symbolId
     * @param int|string $attribute
     * @param Closure $action
     * @throws Exception
     */
    public function addTokenAction(int $symbolId, $attribute, Closure $action): void
    {
        if (isset($this->tokenActionMap[$symbolId][$attribute])) {
            throw new Exception("Action for terminal symbol {$symbolId}->{$attribute} is already set");
        }
        $this->tokenActionMap[$symbolId][$attribute] = $action;
    }

    /**
     * @param Production $production
     * @param int|string $attribute
     * @param Closure $action
     * @throws Exception
     */
    public function addProductionAction(Production $production, $attribute, Closure $action): void
    {
        $headerId = $production->getHeaderId();
        $productionIndex = $production->getIndex();
        if (isset($this->productionActionMap[$headerId][$productionIndex][$attribute])) {
            throw new Exception("Action for production {$headerId}:{$productionIndex}->{$attribute} is already set");
        }
        $this->productionActionMap[$headerId][$productionIndex][$attribute] = $action;
    }

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     * @throws Exception
     */
    public function applySymbolActions(ParsedProduction $production, int $symbolIndex): void
    {
        $context = $this
            ->contextFactory
            ->createSymbolContext($production, $symbolIndex);
        foreach ($this->getSymbolActionList($production, $symbolIndex) as $attribute => $action) {
            $production
                ->getSymbol($symbolIndex)
                ->setAttribute($attribute, $action($context));
        }
    }

    /**
     * @param ParsedSymbol $symbol
     * @param ParsedToken $token
     * @throws Exception
     */
    public function applyTokenActions(ParsedSymbol $symbol, ParsedToken $token): void
    {
        $context = $this->contextFactory->createTokenContext($symbol, $token);
        foreach ($this->getTokenActionList($symbol) as $attribute => $action) {
            $value = $action($context);
            if (is_string($attribute)) {
                $symbol->setAttribute($attribute, $value);
            }
        }
    }

    /**
     * @param ParsedProduction $production
     * @throws Exception
     */
    public function applyProductionActions(ParsedProduction $production): void
    {
        $context = $this->contextFactory->createProductionContext($production);
        $actionList = $this->getProductionActionList($production);
        foreach ($actionList as $attribute => $action) {
            $value = $action($context);
            if (is_string($attribute)) {
                $production
                    ->getHeader()
                    ->setAttribute($attribute, $value);
            }
        }
    }

    /**
     * @param ParsedProduction $production
     * @return Closure[]
     */
    public function getProductionActionList(ParsedProduction $production): array
    {
        $headerId = $production
            ->getHeader()
            ->getSymbolId();
        $productionIndex = $production->getIndex();
        return $this->productionActionMap[$headerId][$productionIndex] ?? [];
    }

    /**
     * @param ParsedSymbol $symbol
     * @return Closure[]
     */
    private function getTokenActionList(ParsedSymbol $symbol): array
    {
        $symbolId = $symbol->getSymbolId();
        return $this->tokenActionMap[$symbolId] ?? [];
    }

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     * @return Closure[]
     */
    private function getSymbolActionList(ParsedProduction $production, int $symbolIndex): array
    {
        $headerId = $production
            ->getHeader()
            ->getSymbolId();
        $productionIndex = $production->getIndex();
        return $this->symbolActionMap[$headerId][$productionIndex][$symbolIndex] ?? [];
    }
}
