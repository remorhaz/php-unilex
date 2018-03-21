<?php

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;
use Remorhaz\UniLex\Token;

interface ParserListenerInterface
{

    public function onStart(): void;

    public function onRootSymbol(Symbol $symbol): void;

    public function onBeginProduction(Production $production): void;

    public function onFinishProduction(Production $production): void;

    public function onSymbol(int $symbolIndex, Production $production): void;

    public function onToken(Symbol $symbol, Token $token): void;

    public function onEoi(Symbol $symbol, Token $token): void;
}
