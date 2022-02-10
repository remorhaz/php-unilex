<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Example\SimpleExpr\Grammar;

abstract class ConfigFile
{
    public static function getPath(): string
    {
        return __DIR__ . "/Config.php";
    }
}
