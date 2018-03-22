<?php

namespace Remorhaz\UniLex\AST;

use Remorhaz\UniLex\Stack\PushInterface;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractTranslatorListener implements TranslatorListenerInterface
{

    public function onBeginProduction(Node $node, PushInterface $stack): void
    {
    }

    public function onFinishProduction(Node $node): void
    {
    }

    public function onSymbol(Symbol $symbol): void
    {
    }
}
