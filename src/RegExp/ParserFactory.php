<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Parser\LL1\Parser;
use Remorhaz\UniLex\Parser\LL1\TranslationSchemeApplier;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\RegExp\Grammar\ConfigFile;
use Remorhaz\UniLex\RegExp\Grammar\TokenMatcher;
use Remorhaz\UniLex\RegExp\Grammar\TranslationScheme;
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
        $scheme = new TranslationScheme($tree);
        $treeBuilder = new TranslationSchemeApplier($scheme);
        $parser = new Parser($grammar, $reader, $treeBuilder);
        $parser->loadLookupTable(ConfigFile::getLookupTablePath());
        return $parser;
    }
}
