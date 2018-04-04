<?php

namespace Remorhaz\UniLex\AST;

use Remorhaz\UniLex\Stack\PushInterface;

interface TranslatorListenerInterface
{

    public function onStart(Node $node): void;

    public function onFinish(): void;

    public function onBeginProduction(Node $node, PushInterface $stack): void;

    public function onFinishProduction(Node $node): void;

    public function onSymbol(Symbol $symbol, PushInterface $stack): void;
}
