<?php

namespace Remorhaz\UniLex\Parser\LL1\SDD;

use Remorhaz\UniLex\Exception;

abstract class RuleSetLoader
{

    const TOKEN_RULE_MAP_KEY = 'token_rules';

    const SYMBOL_RULE_MAP_KEY = 'symbol_rules';

    const PRODUCTION_RULE_MAP_KEY = 'production_rules';

    /**
     * @param ContextFactoryInterface $contextFactory
     * @param array $config
     * @return RuleSet
     * @throws Exception
     */
    public static function loadConfig(ContextFactoryInterface $contextFactory, array $config): RuleSet
    {
        $ruleSet = new RuleSet($contextFactory);
        $symbolRuleMap = self::getConfigValue($config, self::SYMBOL_RULE_MAP_KEY);
        foreach ($symbolRuleMap as $headerId => $productionMap) {
            foreach ($productionMap as $productionIndex => $symbolMap) {
                foreach ($symbolMap as $symbolIndex => $rule) {
                    $ruleSet->addSymbolRule($headerId, $productionIndex, $symbolIndex, $rule);
                }
            }
        }
        $productionRuleMap = self::getConfigValue($config, self::PRODUCTION_RULE_MAP_KEY);
        foreach ($productionRuleMap as $headerId => $productionMap) {
            foreach ($productionMap as $productionIndex => $rule) {
                $ruleSet->addProductionRule($headerId, $productionIndex, $rule);
            }
        }
        $tokenRuleMap = self::getConfigValue($config, self::TOKEN_RULE_MAP_KEY);
        foreach ($tokenRuleMap as $symbolId => $rule) {
            $ruleSet->addTokenRule($symbolId, $rule);
        }
        return $ruleSet;
    }

    /**
     * @param ContextFactoryInterface $contextFactory
     * @param string $fileName
     * @return RuleSet
     * @throws Exception
     */
    public static function loadFile(ContextFactoryInterface $contextFactory, string $fileName): RuleSet
    {
        /** @noinspection PhpIncludeInspection */
        $config = @include $fileName;
        if (false === $config) {
            throw new Exception("Config file {$fileName} not found");
        }
        return self::loadConfig($contextFactory, $config);
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
