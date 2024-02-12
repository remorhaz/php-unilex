<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Console;

use PhpParser\Node\Expr;
use PhpParser\PrettyPrinter\Standard;

final class PrettyPrinter extends Standard
{
    protected function pExpr_Array(Expr\Array_ $node): string
    {
        // $this->options was replaced by set of specific properties in 5.0
        $shortArraySyntax = $this->options['shortArraySyntax'] ?? $this->shortArraySyntax ?? false;
        $syntax = $node->getAttribute(
            'kind',
            $shortArraySyntax ? Expr\Array_::KIND_SHORT : Expr\Array_::KIND_LONG,
        );

        return $syntax === Expr\Array_::KIND_SHORT
            ? '[' . $this->pCommaSeparatedMultiline($node->items, true) . $this->nl . ']'
            : 'array(' . $this->pCommaSeparatedMultiline($node->items, true) . $this->nl . ')';
    }
}
