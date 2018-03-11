<?php

namespace Remorhaz\UniLex\LL1Parser\SDD;

use Remorhaz\UniLex\Exception;

abstract class RuleSetLoader
{

    const TOKEN_RULE_MAP_KEY = 'token_rules';

    const SYMBOL_RULE_MAP_KEY = 'symbol_rules';

    /**
     * @param AbstractRuleSet $ruleSet
     * @param array $config
     * @throws Exception
     * @todo Find the way to use ruleset without overriding a class.
     */
    public static function loadConfig(AbstractRuleSet $ruleSet, array $config): void
    {
        $symbolRuleMap = self::getConfigValue($config, self::SYMBOL_RULE_MAP_KEY);
        foreach ($symbolRuleMap as $headerId => $productionMap) {
            foreach ($productionMap as $productionIndex => $symbolMap) {
                foreach ($symbolMap as $symbolIndex => $rule) {
                    $ruleSet->addSymbolRule($headerId, $productionIndex, $symbolIndex, $rule);
                }
            }
        }
        $tokenRuleMap = self::getConfigValue($config, self::TOKEN_RULE_MAP_KEY);
        foreach ($tokenRuleMap as $symbolId => $rule) {
            $ruleSet->addTokenRule($symbolId, $rule);
        }
    }

    /**
     * @param AbstractRuleSet $ruleSet
     * @param string $fileName
     * @throws Exception
     */
    public static function loadFile(AbstractRuleSet $ruleSet, string $fileName): void
    {
        /** @noinspection PhpIncludeInspection */
        $config = @include $fileName;
        if (false === $config) {
            throw new Exception("Config file {$fileName} not found");
        }
        self::loadConfig($ruleSet, $config);
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
