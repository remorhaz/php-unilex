<?php

namespace Remorhaz\UniLex\LL1Parser\SDD;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\LL1Parser\AbstractParserListener;
use Remorhaz\UniLex\LL1Parser\ParsedProduction;
use Remorhaz\UniLex\LL1Parser\ParsedSymbol;
use Remorhaz\UniLex\LL1Parser\ParsedToken;

class RuleSetApplier extends AbstractParserListener
{

    private $ruleSet;

    public function __construct(AbstractRuleSet $ruleSet)
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
        $this
            ->ruleSet
            ->applySymbolRuleIfExists($production, $symbolIndex);
    }

    /**
     * @param ParsedSymbol $symbol
     * @param ParsedToken $token
     * @throws Exception
     */
    public function onToken(ParsedSymbol $symbol, ParsedToken $token): void
    {
        $this
            ->ruleSet
            ->applyTokenRuleIfExists($symbol, $token);
    }
}
