<?php

namespace Remorhaz\UniLex\RegExp\Grammar;

abstract class ConfigFile
{

    public static function getPath(): string
    {
        return realpath(__DIR__ . "/Config.php");
    }

    public static function getLookupTablePath(): string
    {
        return
            realpath(__DIR__ . "/../../../generated/RegExp/Grammar") . DIRECTORY_SEPARATOR . "LookupTable.php";
    }
}
