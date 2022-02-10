<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Example\Brainfuck;

use Remorhaz\UniLex\Example\Brainfuck\Grammar\TokenMatcher;
use Remorhaz\UniLex\Example\Brainfuck\Grammar\TranslationScheme;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Parser\LL1\Parser;
use Remorhaz\UniLex\Parser\LL1\TranslationSchemeApplier;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

class Interpreter
{
    private $output;


    /**
     * @param string $text
     * @throws Exception
     * @throws \Remorhaz\UniLex\Exception
     */
    public function exec(string $text): void
    {
        unset($this->output);
        $runtime = new Runtime();
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
     * @param string $text
     * @param Runtime $runtime
     * @return Parser
     * @throws \Remorhaz\UniLex\Exception
     */
    private function createParser(string $text, Runtime $runtime): Parser
    {
        $buffer = CharBufferFactory::createFromString($text);
        $grammar = GrammarLoader::loadFile(__DIR__ . "/Grammar/Config.php");
        $tokenReader = new TokenReader($buffer, new TokenMatcher(), new TokenFactory($grammar));
        $translator = new TranslationSchemeApplier(new TranslationScheme($runtime));
        $parser = new Parser($grammar, $tokenReader, $translator);
        $parser->loadLookupTable(__DIR__ . "/Grammar/LookupTable.php");
        return $parser;
    }
}
