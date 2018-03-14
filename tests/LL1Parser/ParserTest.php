<?php

namespace Remorhaz\UniLex\Test\LL1Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Parser\LL1\AbstractParserListener;
use Remorhaz\UniLex\Parser\LL1\Parser;
use Remorhaz\UniLex\CharBuffer;
use Remorhaz\UniLex\TokenReader;
use Remorhaz\UniLex\TokenMatcherByType;

/**
 * @covers \Remorhaz\UniLex\Parser\LL1\Parser
 */
class ParserTest extends TestCase
{

    /**
     * @param string $configFile
     * @param int[] $input
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidGrammarInput
     */
    public function testParse_ValidBuffer_OnTokenTriggeredForEachToken(string $configFile, array $input): void
    {
        $grammar = GrammarLoader::loadFile($configFile);
        $buffer = CharBuffer::fromSymbols(...$input);
        $reader = new TokenReader($buffer, new TokenMatcherByType, new TokenFactory($grammar));
        $listener = $this
            ->createMock(AbstractParserListener::class);
        $listener
            ->expects($this->exactly(count($input)))
            ->method('onToken');

        /** @var AbstractParserListener $listener */
        $parser = new Parser($grammar, $reader, $listener);
        $parser->run();
    }

    public function providerValidGrammarInput(): array
    {
        $data = [];
        $inputList = [
            "id+id*id" => [
                TokenType::ID,
                TokenType::PLUS,
                TokenType::ID,
                TokenType::STAR,
                TokenType::ID,
            ],
        ];
        foreach ($inputList as $inputText => $input) {
            $data["SimpleExpr example: {$inputText}"] =
                [ConfigFile::getPath(), $input];
        }
        return $data;
    }
}
