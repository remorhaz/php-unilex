<?php

namespace Remorhaz\UniLex\Test\LL1Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Grammar\ContextFreeGrammarLoader;
use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeMatcherInterface;
use Remorhaz\UniLex\LL1Parser\AbstractParserListener;
use Remorhaz\UniLex\LL1Parser\Parser;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferInterface;
use Remorhaz\UniLex\SymbolBufferLexemeReader;
use Remorhaz\UniLex\Unicode\SymbolLexeme;
use SplFixedArray;

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
        $grammar = ContextFreeGrammarLoader::loadFile($configFile);
        $buffer = new SymbolBuffer(SplFixedArray::fromArray($input));
        $matcher = $this->createLexemeMatcher();
        $reader = new SymbolBufferLexemeReader($buffer, $matcher, $grammar->getEoiSymbol());
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

    /**
     * @return LexemeMatcherInterface
     * @todo Move to examples and refactor the way of getting example data.
     */
    private function createLexemeMatcher(): LexemeMatcherInterface
    {
        return new class implements LexemeMatcherInterface {

            public function match(SymbolBufferInterface $buffer): Lexeme
            {
                $lexeme = new SymbolLexeme($buffer->getLexemeInfo(), $buffer->getSymbol());
                $buffer->nextSymbol();
                return $lexeme;
            }
        };
    }
}
