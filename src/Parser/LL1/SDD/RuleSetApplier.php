<?php

namespace Remorhaz\UniLex\Parser\LL1\SDD;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Parser\LL1\AbstractParserListener;
use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Parser\ParsedToken;

class RuleSetApplier extends AbstractParserListener
{

    private $ruleSet;

    public function __construct(RuleSet $ruleSet)
    {
        $this->ruleSet = $ruleSet;
    }

    /**
     * @param int $symbolIndex
     * @param ParsedProduction $production
     * @throws Exception
     */
    public function onSymbol(int $symbolIndex, ParsedProduction $production): void
    {
        //echo "Symbol {$production}[{$symbolIndex}]->{$production->getSymbol($symbolIndex)->getSymbolId()}", PHP_EOL;
        $this
            ->ruleSet
            ->applySymbolRules($production, $symbolIndex);
    }

    /**
     * @param ParsedSymbol $symbol
     * @param ParsedToken $token
     * @throws Exception
     */
    public function onToken(ParsedSymbol $symbol, ParsedToken $token): void
    {
        //echo "Token {$symbol->getSymbolId()} -> {$token->getToken()->getType()}", PHP_EOL;
        $this
            ->ruleSet
            ->applyTokenRuleIfExists($symbol, $token);
    }

    /**
     * @param ParsedProduction $production
     * @throws Exception
     */
    public function onFinishProduction(ParsedProduction $production): void
    {
        //echo "Finish {$production}", PHP_EOL;
        $this
            ->ruleSet
            ->applyProductionRules($production);
    }

    public function onBeginProduction(ParsedProduction $production): void
    {
        //echo "Begin {$production}", PHP_EOL;
    }

    public function onRootSymbol(ParsedSymbol $symbol): void
    {
        //echo "Root symbol {$symbol->getSymbolId()}", PHP_EOL;
    }
}
