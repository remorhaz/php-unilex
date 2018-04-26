<?php

namespace Remorhaz\UniLex\Unicode\Grammar;

abstract class TokenAttribute
{
    const UNICODE_CHAR = 'unicode.char';

    const UNICODE_CHAR_OFFSET_START = 'unicode.char_offset.start';
    const UNICODE_CHAR_OFFSET_FINISH = 'unicode.char_offset.finish';
    const UNICODE_BYTE_OFFSET_START = 'unicode.byte_offset.start';
    const UNICODE_BYTE_OFFSET_FINISH = 'unicode.byte_offset.finish';
}
