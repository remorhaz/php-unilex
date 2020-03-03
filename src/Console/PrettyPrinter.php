<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Console;

use PhpParser\Node\Expr;
use PhpParser\PrettyPrinter\Standard;

final class PrettyPrinter extends Standard
{

    protected function pExpr_Array(Expr\Array_ $node)
    {
        $syntax = $node->getAttribute(
            'kind',
            $this->options['shortArraySyntax'] ? Expr\Array_::KIND_SHORT : Expr\Array_::KIND_LONG
        );
        if ($syntax === Expr\Array_::KIND_SHORT) {
            return '[' . $this->pCommaSeparatedMultiline($node->items, true) . $this->nl . ']';
        } else {
            return 'array(' . $this->pCommaSeparatedMultiline($node->items, true) . $this->nl . ')';
        }
    }
}
