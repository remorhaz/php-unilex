<?php

namespace Remorhaz\UniLex\Grammar;

use Remorhaz\UniLex\Exception;

abstract class ContextFreeGrammarLoader
{

    public const TOKEN_MAP_KEY = 'tokens';

    public const PRODUCTION_MAP_KEY = 'productions';

    public const START_SYMBOL_KEY = 'start_symbol';

    public const EOI_SYMBOL_KEY = 'eoi_symbol';

    /**
     * @param array $config
     * @return ContextFreeGrammar
     * @throws Exception
     */
    public static function loadConfig(array $config): ContextFreeGrammar
    {
        $tokenMap = self::getConfigValue($config, self::TOKEN_MAP_KEY);
        $productionMap = self::getConfigValue($config, self::PRODUCTION_MAP_KEY);
        $startSymbol = self::getConfigValue($config, self::START_SYMBOL_KEY);
        $eoiSymbol = self::getConfigValue($config, self::EOI_SYMBOL_KEY);
        $grammar = new ContextFreeGrammar($startSymbol, $eoiSymbol);
        self::loadFromMaps($grammar, $tokenMap, $productionMap);
        return $grammar;
    }

    /**
     * @param string $fileName
     * @return ContextFreeGrammar
     * @throws Exception
     */
    public static function loadFile(string $fileName): ContextFreeGrammar
    {
        /** @noinspection PhpIncludeInspection */
        $config = @include $fileName;
        if (false === $config) {
            throw new Exception("Config file {$fileName} not found");
        }
        return self::loadConfig($config);
    }

    /**
     * @param mixed $config
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    private static function getConfigValue($config, string $key)
    {
        if (!is_array($config)) {
            throw new Exception("Config should be an array");
        }
        if (!isset($config[$key])) {
            throw new Exception("Key '{$key}' not found in config");
        }
        return $config[$key];
    }

    private static function loadFromMaps(ContextFreeGrammar $grammar, array $tokenMap, array $productionMap): void
    {
        foreach ($tokenMap as $symbolId => $tokenIdList) {
            $grammar->addToken($symbolId, ...$tokenIdList);
        }
        foreach ($productionMap as $symbolId => $productionList) {
            $grammar->addProduction($symbolId, ...$productionList);
        }
    }

}
