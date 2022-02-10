<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\AST;

use Remorhaz\UniLex\Stack\PushInterface;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractTranslatorListener implements TranslatorListenerInterface
{
    public function onStart(Node $node): void
    {
    }

    public function onFinish(): void
    {
    }

    public function onBeginProduction(Node $node, PushInterface $stack): void
    {
    }

    public function onFinishProduction(Node $node): void
    {
    }

    public function onSymbol(Symbol $symbol, PushInterface $stack): void
    {
    }
}
