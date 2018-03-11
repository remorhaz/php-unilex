<?php

namespace Remorhaz\UniLex\RegExp\Grammar\SDD;

abstract class ConfigFile
{

    public static function getPath(): string
    {
        return realpath(__DIR__ . "/Config.php");
    }
}
