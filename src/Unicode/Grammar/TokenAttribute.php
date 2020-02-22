<?php

namespace Remorhaz\UniLex\Unicode\Grammar;

abstract class TokenAttribute
{
    public const UNICODE_CHAR = 'unicode.char';

    public const UNICODE_CHAR_OFFSET = 'unicode.char_offset.start';
    public const UNICODE_CHAR_LENGTH = 'unicode.char_offset.finish';
    public const UNICODE_BYTE_OFFSET = 'unicode.byte_offset.start';
    public const UNICODE_BYTE_LENGTH = 'unicode.byte_offset.finish';
}
