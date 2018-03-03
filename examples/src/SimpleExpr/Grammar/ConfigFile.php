<?php

namespace Remorhaz\UniLex\Example\SimpleExpr\Grammar;

abstract class ConfigFile
{

    public static function getPath(): string
    {
        return realpath(__DIR__ . "/Config.php");
    }
}
