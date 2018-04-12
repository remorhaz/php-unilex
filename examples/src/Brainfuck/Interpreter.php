<?php

namespace Remorhaz\UniLex\Example\Brainfuck;

use Remorhaz\UniLex\Example\Brainfuck\Grammar\TokenMatcher;
use Remorhaz\UniLex\Example\Brainfuck\Grammar\TranslationScheme;
use Remorhaz\UniLex\Grammar\ContextFree\Grammar;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Parser\LL1\Parser;
use Remorhaz\UniLex\Parser\LL1\TranslationSchemeApplier;
use Remorhaz\UniLex\TokenReader;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

class Interpreter
{

    private $grammar;

    private $output;


    /**
     * @param string $text
     * @throws \Remorhaz\UniLex\Exception
     * @throws Exception
     */
    public function exec(string $text): void
    {
        unset($this->output);
        $runtime = new Runtime;
        $this
            ->createParser($text, $runtime)
            ->run();
        $runtime->exec();
        $this->output = $runtime->getOutput();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getOutput(): string
    {
        if (!isset($this->output)) {
            throw new Exception("Output is not defined");
        }
        return $this->output;
    }

    /**
     * @return Grammar
     * @throws \Remorhaz\UniLex\Exception
     */
    private function getGrammar(): Grammar
    {
        if (!isset($this->grammar)) {
            $this->grammar = GrammarLoader::loadFile(__DIR__ . "/Grammar/Config.php");
        }
        return $this->grammar;
    }

    /**
     * @param string $text
     * @param Runtime $runtime
     * @return Parser
     * @throws \Remorhaz\UniLex\Exception
     */
    private function createParser(string $text, Runtime $runtime): Parser
    {
        $buffer = CharBufferFactory::createFromUtf8String($text);
        $tokenReader = new TokenReader($buffer, new TokenMatcher, new TokenFactory($this->getGrammar()));
        $translator = new TranslationSchemeApplier(new TranslationScheme($runtime));
        $parser = new Parser($this->getGrammar(), $tokenReader, $translator);
        $parser->loadLookupTable(__DIR__ . "/../../generated/Brainfuck/Grammar/LookupTable.php");
        return $parser;
    }
}
