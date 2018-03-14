<?php

namespace Remorhaz\UniLex\Grammar\SDD;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;

abstract class TranslationSchemeLoader
{

    const TOKEN_RULE_MAP_KEY = 'token_rules';

    const SYMBOL_RULE_MAP_KEY = 'symbol_rules';

    const PRODUCTION_RULE_MAP_KEY = 'production_rules';

    /**
     * @param GrammarInterface $grammar
     * @param ContextFactoryInterface $contextFactory
     * @param array $config
     * @return TranslationScheme
     * @throws Exception
     */
    public static function loadConfig(
        GrammarInterface $grammar,
        ContextFactoryInterface $contextFactory,
        array $config
    ): TranslationScheme {
        $ruleSet = new TranslationScheme($contextFactory);
        $symbolRuleMap = self::getConfigValue($config, self::SYMBOL_RULE_MAP_KEY);
        foreach ($symbolRuleMap as $headerId => $productionMap) {
            foreach ($productionMap as $productionIndex => $symbolMap) {
                $production = $grammar->getProduction($headerId, $productionIndex);
                foreach ($symbolMap as $symbolIndex => $actionList) {
                    foreach ($actionList as $attribute => $action) {
                        $ruleSet->addSymbolAction($production, $symbolIndex, $attribute, $action);
                    }
                }
            }
        }
        $productionRuleMap = self::getConfigValue($config, self::PRODUCTION_RULE_MAP_KEY);
        foreach ($productionRuleMap as $headerId => $productionMap) {
            foreach ($productionMap as $productionIndex => $actionList) {
                $production = $grammar->getProduction($headerId, $productionIndex);
                foreach ($actionList as $attribute => $action) {
                    $ruleSet->addProductionAction($production, $attribute, $action);
                }
            }
        }
        $tokenRuleMap = self::getConfigValue($config, self::TOKEN_RULE_MAP_KEY);
        foreach ($tokenRuleMap as $symbolId => $actionList) {
            foreach ($actionList as $attribute => $action) {
                $ruleSet->addTokenAction($symbolId, $attribute, $action);
            }
        }
        return $ruleSet;
    }

    /**
     * @param GrammarInterface $grammar
     * @param ContextFactoryInterface $contextFactory
     * @param string $fileName
     * @return TranslationScheme
     * @throws Exception
     */
    public static function loadFile(
        GrammarInterface $grammar,
        ContextFactoryInterface $contextFactory,
        string $fileName
    ): TranslationScheme {
        /** @noinspection PhpIncludeInspection */
        $config = @include $fileName;
        if (false === $config) {
            throw new Exception("Config file {$fileName} not found");
        }
        return self::loadConfig($grammar, $contextFactory, $config);
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
}
