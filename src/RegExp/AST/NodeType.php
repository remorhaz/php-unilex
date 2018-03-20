<?php

namespace Remorhaz\UniLex\RegExp\AST;

abstract class NodeType
{
    public const EMPTY = 'empty';
    public const SYMBOL = 'symbol';
    public const SYMBOL_ANY = 'symbol_any';
    public const CONCATENATE = 'concatenate';
    public const REPEAT = 'repeat';
    public const ALTERNATIVE = 'alternative';
    public const ASSERT = 'assert';
    public const ESC_SIMPLE = 'esc_simple';
    public const SYMBOL_RANGE = 'symbol_range';
    public const SYMBOL_CLASS = 'symbol_class';
    public const SYMBOL_PROP = 'symbol_prop';
    public const SYMBOL_CTL = 'symbol_ctl';
}
