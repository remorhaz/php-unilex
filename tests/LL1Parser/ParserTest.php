<?php

namespace Remorhaz\UniLex\Test\LL1Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\LexemeFactory;
use Remorhaz\UniLex\LL1Parser\AbstractParserListener;
use Remorhaz\UniLex\LL1Parser\Parser;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\LexemeReader;
use Remorhaz\UniLex\LexemeMatcherByType;

/**
 * @covers \Remorhaz\UniLex\LL1Parser\Parser
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
    public function testParse_ValidBuffer_OnLexemeTriggeredForEachToken(string $configFile, array $input): void
    {
        $grammar = GrammarLoader::loadFile($configFile);
        $lexemeFactory = new LexemeFactory($grammar);
        $buffer = SymbolBuffer::fromSymbols(...$input);
        $reader = new LexemeReader($buffer, new LexemeMatcherByType, $lexemeFactory);
        $listener = $this
            ->createMock(AbstractParserListener::class);
        $listener
            ->expects($this->exactly(count($input)))
            ->method('onLexeme');

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
