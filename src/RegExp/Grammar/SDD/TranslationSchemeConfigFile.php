<?php

namespace Remorhaz\UniLex\RegExp\Grammar\SDD;

abstract class TranslationSchemeConfigFile
{

    public static function getPath(): string
    {
        return realpath(__DIR__ . "/TranslationSchemeConfig.php");
    }
}
