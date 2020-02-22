<?php

namespace Remorhaz\UniLex\RegExp\Grammar;

abstract class ConfigFile
{

    public static function getPath(): string
    {
        return __DIR__ . "/Config.php";
    }

    public static function getLookupTablePath(): string
    {
        return __DIR__ . "/../../../src/RegExp/Grammar" . DIRECTORY_SEPARATOR . "LookupTable.php";
    }
}
