<?php

namespace Remorhaz\UniLex\RegExp\Grammar;

abstract class ConfigFile
{

    public static function getPath(): string
    {
        return realpath(__DIR__ . "/Config.php");
    }
}
