<?php

namespace Remorhaz\UniLex\Unicode\Grammar;

abstract class ConfigFile
{

    public static function getPath(): string
    {
        return realpath(__DIR__ . "/Config.php");
    }
}
