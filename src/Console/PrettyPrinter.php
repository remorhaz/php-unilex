<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Console;

use PhpParser\Node\Expr;
use PhpParser\PrettyPrinter\Standard;

final class PrettyPrinter extends Standard
{
    protected function pExpr_Array(Expr\Array_ $node): string
    {
        $syntax = $node->getAttribute(
            'kind',
            $this->options['shortArraySyntax'] ? Expr\Array_::KIND_SHORT : Expr\Array_::KIND_LONG,
        );

        return $syntax === Expr\Array_::KIND_SHORT
            ? '[' . $this->pCommaSeparatedMultiline($node->items, true) . $this->nl . ']'
            : 'array(' . $this->pCommaSeparatedMultiline($node->items, true) . $this->nl . ')';
    }
}
