<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Parser\LL1;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\Production;
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
     * @param list<int> $input
     * @throws UniLexException
     * @dataProvider providerValidGrammarInput
     */
    public function testRun_ValidBuffer_OnTokenTriggeredForEachToken(string $configFile, array $input): void
    {
        $grammar = GrammarLoader::loadFile($configFile);
        $buffer = new CharBuffer(...$input);
        $reader = new TokenReader($buffer, new TokenMatcherByType(), new TokenFactory($grammar));
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
     * @throws UnexpectedTokenException
     * @throws UniLexException
     */
    public function testRun_InvalidBuffer_ThrowsException(): void
    {
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $input = [TokenType::ID, TokenType::PLUS, TokenType::STAR];
        $buffer = new CharBuffer(...$input);
        $reader = new TokenReader($buffer, new TokenMatcherByType(), new TokenFactory($grammar));
        $listener = $this->createMock(AbstractParserListener::class);
        /** @var AbstractParserListener $listener */
        $parser = new Parser($grammar, $reader, $listener);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Unexpected token: 2');
        $parser->run();
    }

    /**
     * @throws UniLexException
     */
    public function testRun_InvalidBuffer_ThrowsErrorInfoWithMatchingExpectedTokenTypeList(): void
    {
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $input = [TokenType::ID, TokenType::PLUS, TokenType::STAR];
        $buffer = new CharBuffer(...$input);
        $reader = new TokenReader($buffer, new TokenMatcherByType(), new TokenFactory($grammar));
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
     * @throws UniLexException
     */
    public function testRun_InvalidBuffer_ThrowsErrorInfoWithMatchingUnexpectedToken(): void
    {
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $input = [TokenType::ID, TokenType::PLUS, TokenType::STAR];
        $buffer = new CharBuffer(...$input);
        $reader = new TokenReader($buffer, new TokenMatcherByType(), new TokenFactory($grammar));
        $listener = $this->createStub(AbstractParserListener::class);
        $parser = new Parser($grammar, $reader, $listener);
        try {
            $parser->run();
            $actualValue = null;
        } catch (UnexpectedTokenException $e) {
            $actualValue = $e->getErrorInfo()->getUnexpectedToken()->getType();
        }
        self::assertSame(TokenType::STAR, $actualValue);
    }

    /**
     * @return iterable<string, array{string, list<int>}>
     */
    public static function providerValidGrammarInput(): iterable
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
            $data["SimpleExpr example: $inputText"] =
                [ConfigFile::getPath(), $input];
        }
        return $data;
    }

    /**
     * @throws UniLexException
     */
    public function testLoadLookupTable_FileDoesNotExist_ThrowsException(): void
    {
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $buffer = new CharBuffer();
        $reader = new TokenReader($buffer, new TokenMatcherByType(), new TokenFactory($grammar));
        $listener = $this->createStub(AbstractParserListener::class);
        $parser = new Parser($grammar, $reader, $listener);
        $this->expectException(UniLexException::class);
        $this->expectExceptionMessageMatches(
            '#Failed to load lookup table from file .+/NotExists\.php#',
        );
        $parser->loadLookupTable(__DIR__ . '/NotExists.php');
    }

    /**
     * @throws UnexpectedTokenException
     * @throws UniLexException
     */
    public function testRun_LookupTableNotLoaded_UsesGrammarToBuildNewTable(): void
    {
        /**
         * The grammar is the following:
         *
         * Productions:
         *   1 -> 2 3 (start production)
         *   2 -> 4 (we need at least one non-terminal to access the table)
         *   4 -> 5
         *
         * Terminals:
         *   3 (EOI)
         *   5
         *
         * Tokens (mapped to terminals):
         *   3 -> 6 (EOI)
         *   5 -> 7
         */
        $grammar = $this->createMock(GrammarInterface::class);
        $grammar
            ->method('getRootSymbol')
            ->willReturn(1);
        $grammar
            ->method('getStartSymbol')
            ->willReturn(2);
        $grammar
            ->method('getEoiSymbol')
            ->willReturn(3);
        $grammar
            ->method('getEoiToken')
            ->willReturn(6);
        $grammar
            ->method('isEoiToken')
            ->willReturnMap(
                [
                    [6, true],
                    [7, false],
                ],
            );
        $grammar
            ->method('isTerminal')
            ->willReturnMap(
                [
                    [1, false],
                    [2, false],
                    [3, true],
                    [4, false],
                    [5, true],
                ],
            );
        $grammar
            ->method('getTerminalList')
            ->willReturn([3, 5]);
        $grammar
            ->method('tokenMatchesTerminal')
            ->willReturnMap(
                [
                    [3, 6, true],
                    [5, 7, true],
                ],
            );
        $grammar
            ->method('getToken')
            ->willReturnMap(
                [
                    [3, 6],
                    [5, 7],
                ],
            );
        $grammar
            ->method('getProduction')
            ->willReturnMap(
                [
                    [1, 0, new Production(1, 0, 2, 3)],
                    [2, 0, new Production(2, 0, 4)],
                    [4, 0, new Production(4, 0, 5)],
                ],
            );
        $grammar
            ->method('getProductionList')
            ->willReturnMap(
                [
                    [1, [new Production(1, 0, 2, 3)]],
                    [2, [new Production(2, 0, 4)]],
                    [4, [new Production(4, 0, 5)]],
                ],
            );
        $grammar
            ->method('getFullProductionList')
            ->willReturn(
                [
                    new Production(1, 0, 2, 3),
                    new Production(2, 0, 4),
                    new Production(4, 0, 5),
                ],
            );
        $buffer = new CharBuffer(7);
        $reader = new TokenReader($buffer, new TokenMatcherByType(), new TokenFactory($grammar));
        $listener = $this->createStub(AbstractParserListener::class);
        $parser = new Parser($grammar, $reader, $listener);
        $grammar
            ->expects(self::atLeastOnce())
            ->method('getNonTerminalList')
            ->willReturn([1, 2, 4]);
        $parser->run();
    }

    /**
     * @throws UnexpectedTokenException
     * @throws UniLexException
     */
    public function testRun_LookUpTableLoaded_NeverUsesGrammarToBuildNewTable(): void
    {
        /**
         * The grammar is the following:
         *
         * Productions:
         *   1 -> 2 3 (start production)
         *   2 -> 4 (we need at least one non-terminal to access the table)
         *   4 -> 5
         *
         * Terminals:
         *   3 (EOI)
         *   5
         *
         * Tokens (mapped to terminals):
         *   3 -> 6 (EOI)
         *   5 -> 7
         */
        $grammar = $this->createMock(GrammarInterface::class);
        $grammar
            ->method('getRootSymbol')
            ->willReturn(1);
        $grammar
            ->method('getStartSymbol')
            ->willReturn(2);
        $grammar
            ->method('getEoiSymbol')
            ->willReturn(3);
        $grammar
            ->method('getEoiToken')
            ->willReturn(6);
        $grammar
            ->method('isEoiToken')
            ->willReturnMap(
                [
                    [6, true],
                    [7, false],
                ],
            );
        $grammar
            ->method('isTerminal')
            ->willReturnMap(
                [
                    [1, false],
                    [2, false],
                    [3, true],
                    [4, false],
                    [5, true],
                ],
            );
        $grammar
            ->method('getTerminalList')
            ->willReturn([3, 5]);
        $grammar
            ->method('tokenMatchesTerminal')
            ->willReturnMap(
                [
                    [3, 6, true],
                    [5, 7, true],
                ],
            );
        $grammar
            ->method('getToken')
            ->willReturnMap(
                [
                    [3, 6],
                    [5, 7],
                ],
            );
        $grammar
            ->method('getProduction')
            ->willReturnMap(
                [
                    [1, 0, new Production(1, 0, 2, 3)],
                    [2, 0, new Production(2, 0, 4)],
                    [4, 0, new Production(4, 0, 5)],
                ],
            );
        $grammar
            ->method('getProductionList')
            ->willReturnMap(
                [
                    [1, [new Production(1, 0, 2, 3)]],
                    [2, [new Production(2, 0, 4)]],
                    [4, [new Production(4, 0, 5)]],
                ],
            );
        $grammar
            ->method('getFullProductionList')
            ->willReturn(
                [
                    new Production(1, 0, 2, 3),
                    new Production(2, 0, 4),
                    new Production(4, 0, 5),
                ],
            );
        $buffer = new CharBuffer(7);
        $reader = new TokenReader($buffer, new TokenMatcherByType(), new TokenFactory($grammar));
        $listener = $this->createStub(AbstractParserListener::class);
        $parser = new Parser($grammar, $reader, $listener);
        $parser->loadLookupTable(__DIR__ . '/LookupTable.php');

        $grammar
            ->expects(self::never())
            ->method('getNonTerminalList')
            ->willReturn([1, 2, 4]);
        $parser->run();
    }
}
