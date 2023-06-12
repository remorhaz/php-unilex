<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Grammar\ContextFree;

use Remorhaz\UniLex\Exception;

abstract class GrammarLoader
{
    public const TOKEN_MAP_KEY = 'tokens';

    public const PRODUCTION_MAP_KEY = 'productions';

    public const ROOT_SYMBOL_KEY = 'root_symbol';

    public const START_SYMBOL_KEY = 'start_symbol';

    public const EOI_SYMBOL_KEY = 'eoi_symbol';

    /**
     * @throws Exception
     */
    public static function loadConfig(mixed $config): Grammar
    {
        $tokenMap = self::getConfigValue($config, self::TOKEN_MAP_KEY);
        $productionMap = self::getConfigValue($config, self::PRODUCTION_MAP_KEY);
        $rootSymbol = self::getConfigValue($config, self::ROOT_SYMBOL_KEY);
        $startSymbol = self::getConfigValue($config, self::START_SYMBOL_KEY);
        $eoiSymbol = self::getConfigValue($config, self::EOI_SYMBOL_KEY);
        $grammar = new Grammar($rootSymbol, $startSymbol, $eoiSymbol);
        self::loadFromMaps($grammar, $tokenMap, $productionMap);

        return $grammar;
    }

    /**
     * @throws Exception
     */
    public static function loadFile(string $fileName): Grammar
    {
        $config = @include $fileName;

        return false === $config
            ? throw new Exception("Config file {$fileName} not found")
            : self::loadConfig($config);
    }

    /**
     * @throws Exception
     */
    private static function getConfigValue(mixed $config, string $key): mixed
    {
        if (!is_array($config)) {
            throw new Exception("Config should be an array");
        }

        if (!isset($config[$key])) {
            throw new Exception("Key '$key' not found in config");
        }

        return $config[$key];
    }

    /**
     * @param Grammar                                  $grammar
     * @param array<int, int>                          $tokenMap
     * @param array<int, array<int, list<Production>>> $productionMap
     * @return void
     */
    private static function loadFromMaps(Grammar $grammar, array $tokenMap, array $productionMap): void
    {
        foreach ($tokenMap as $symbolId => $tokenId) {
            $grammar->addToken($symbolId, $tokenId);
        }
        foreach ($productionMap as $symbolId => $productionList) {
            foreach ($productionList as $production) {
                $grammar->addProduction($symbolId, ...$production);
            }
        }
    }
}
