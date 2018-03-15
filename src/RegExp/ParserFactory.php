<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeLoader;
use Remorhaz\UniLex\Parser\LL1\Parser;
use Remorhaz\UniLex\Parser\LL1\TranslationSchemeApplier;
use Remorhaz\UniLex\Parser\SyntaxTree\SDD\ContextFactory;
use Remorhaz\UniLex\Parser\SyntaxTree\Tree;
use Remorhaz\UniLex\RegExp\Grammar\ConfigFile;
use Remorhaz\UniLex\RegExp\Grammar\TranslationSchemeConfig;
use Remorhaz\UniLex\TokenReader;

abstract class ParserFactory
{

    /**
     * @param Tree $tree
     * @param CharBufferInterface $buffer
     * @return Parser
     * @throws \Remorhaz\UniLex\Exception
     */
    public static function createFromBuffer(Tree $tree, CharBufferInterface $buffer): Parser
    {
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $reader = new TokenReader($buffer, new TokenMatcher, new TokenFactory($grammar));
        $contextFactory = new ContextFactory($tree);
        $scheme = TranslationSchemeLoader::loadConfig($grammar, $contextFactory, TranslationSchemeConfig::get());
        $treeBuilder = new TranslationSchemeApplier($scheme);
        $parser = new Parser($grammar, $reader, $treeBuilder);
        $parser->loadLookupTable(ConfigFile::getLookupTablePath());
        return $parser;
    }
}
