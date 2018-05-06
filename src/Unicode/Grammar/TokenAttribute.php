<?php

namespace Remorhaz\UniLex\Unicode\Grammar;

abstract class TokenAttribute
{
    const UNICODE_CHAR = 'unicode.char';

    const UNICODE_CHAR_OFFSET = 'unicode.char_offset.start';
    const UNICODE_CHAR_LENGTH = 'unicode.char_offset.finish';
    const UNICODE_BYTE_OFFSET = 'unicode.byte_offset.start';
    const UNICODE_BYTE_LENGTH = 'unicode.byte_offset.finish';
}
