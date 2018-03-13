<?php

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Parser\ParsedToken;

abstract class AbstractParserListener implements ParserListenerInterface
{

    public function onStart(): void
    {
    }

    public function onRootSymbol(ParsedSymbol $symbol): void
    {
    }

    public function onBeginProduction(ParsedProduction $production): void
    {
    }

    public function onFinishProduction(ParsedProduction $production): void
    {
    }

    public function onSymbol(int $symbolIndex, ParsedProduction $production): void
    {
    }

    public function onToken(ParsedSymbol $symbol, ParsedToken $token): void
    {
    }

    public function onEoi(ParsedSymbol $symbol, ParsedToken $token): void
    {
    }
}
