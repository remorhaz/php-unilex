<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Grammar;

abstract class ConfigFile
{
    public static function getPath(): string
    {
        return __DIR__ . "/Config.php";
    }

    public static function getLookupTablePath(): string
    {
        return __DIR__ . "/LookupTable.php";
    }
}
