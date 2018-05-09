<?php

namespace Remorhaz\UniLex\Test\Parser\LL1;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Parser\LL1\AbstractParserListener;
use Remorhaz\UniLex\Parser\LL1\Parser;
use Remorhaz\UniLex\IO\CharBuffer;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\Lexer\TokenMatcherByType;
use Remorhaz\UniLex\Parser\LL1\UnexpectedTokenException;

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
        $buffer = new CharBuffer(...$input);
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

    /**
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Parser\LL1\UnexpectedTokenException
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Parser\LL1\UnexpectedTokenException
     * @expectedExceptionMessage Unexpected token: 2
     */
    public function testParse_InvalidBuffer_ThrowsException(): void
    {
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $input = [TokenType::ID, TokenType::PLUS, TokenType::STAR];
        $buffer = new CharBuffer(...$input);
        $reader = new TokenReader($buffer, new TokenMatcherByType, new TokenFactory($grammar));
        $listener = $this->createMock(AbstractParserListener::class);
        /** @var AbstractParserListener $listener */
        $parser = new Parser($grammar, $reader, $listener);
        $parser->run();
    }

    /**
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testParse_InvalidBuffer_ThrowsErrorInfoWithMatchingExpectedTokenTypeList(): void
    {
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $input = [TokenType::ID, TokenType::PLUS, TokenType::STAR];
        $buffer = new CharBuffer(...$input);
        $reader = new TokenReader($buffer, new TokenMatcherByType, new TokenFactory($grammar));
        $listener = $this->createMock(AbstractParserListener::class);
        /** @var AbstractParserListener $listener */
        $parser = new Parser($grammar, $reader, $listener);
        try {
            $parser->run();
        } catch (UnexpectedTokenException $e) {
            $actualValue = $e->getErrorInfo()->getExpectedTokenTypeList();
            sort($actualValue);
            $expectedValue = [TokenType::L_PARENTHESIS, TokenType::ID];
            self::assertSame($expectedValue, $actualValue);
        }
    }


    /**
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testParse_InvalidBuffer_ThrowsErrorInfoWithMatchingUnexpectedToken(): void
    {
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $input = [TokenType::ID, TokenType::PLUS, TokenType::STAR];
        $buffer = new CharBuffer(...$input);
        $reader = new TokenReader($buffer, new TokenMatcherByType, new TokenFactory($grammar));
        $listener = $this->createMock(AbstractParserListener::class);
        /** @var AbstractParserListener $listener */
        $parser = new Parser($grammar, $reader, $listener);
        try {
            $parser->run();
        } catch (UnexpectedTokenException $e) {
            $actualValue = $e->getErrorInfo()->getUnexpectedToken()->getType();
            self::assertSame(TokenType::STAR, $actualValue);
        }
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
