<?php

namespace Remorhaz\UniLex\Example\Brainfuck;

use Remorhaz\UniLex\CharBuffer;
use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\Example\Brainfuck\Grammar\TokenMatcher;
use Remorhaz\UniLex\Example\Brainfuck\Grammar\TranslationScheme;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory as BrainfuckTokenFactory;
use Remorhaz\UniLex\Parser\LL1\Parser;
use Remorhaz\UniLex\Parser\LL1\TranslationSchemeApplier;
use Remorhaz\UniLex\TokenBuffer;
use Remorhaz\UniLex\TokenMatcherInterface;
use Remorhaz\UniLex\TokenReader;
use Remorhaz\UniLex\Unicode\CharFactory;
use Remorhaz\UniLex\Unicode\Grammar\Utf8TokenMatcher;
use Remorhaz\UniLex\Unicode\Grammar\TokenFactory as UnicodeTokenFactory;

class Interpreter
{

    private $unicodeMatcher;


    /**
     * @param string $text
     * @throws \Remorhaz\UniLex\Exception
     */
    public function exec(string $text): void
    {
        $buffer = $this->createUnicodeBuffer($text);
        $grammar = GrammarLoader::loadFile(__DIR__ . "/Grammar/Config.php");
        $tokenReader = new TokenReader($buffer, new TokenMatcher, new BrainfuckTokenFactory($grammar));
        $translator = new TranslationSchemeApplier(new TranslationScheme());
        $parser = new Parser($grammar, $tokenReader, $translator);
        $parser->run();
    }

    private function getUnicodeMatcher(): TokenMatcherInterface
    {
        if (!isset($this->unicodeMatcher)) {
            $this->unicodeMatcher = new Utf8TokenMatcher;
        }
        return $this->unicodeMatcher;
    }

    private function createUnicodeBuffer(string $text): CharBufferInterface
    {
        $textBuffer = CharBuffer::fromString($text);
        $tokenReader = new TokenReader($textBuffer, $this->getUnicodeMatcher(), new UnicodeTokenFactory);
        return new TokenBuffer($tokenReader, new CharFactory);
    }
}
