<?php

namespace Remorhaz\UniLex\RegExp\Grammar;

abstract class TranslationSchemeConfigFile
{

    public static function getPath(): string
    {
        return realpath(__DIR__ . "/TranslationSchemeConfig.php");
    }
}
