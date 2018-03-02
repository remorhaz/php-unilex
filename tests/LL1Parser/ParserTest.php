<?php

namespace Remorhaz\UniLex\Test\LL1Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFreeGrammar;
use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeMatcherInterface;
use Remorhaz\UniLex\LL1Parser\AbstractParserListener;
use Remorhaz\UniLex\LL1Parser\Parser;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferInterface;
use Remorhaz\UniLex\SymbolBufferLexemeReader;
use Remorhaz\UniLex\Unicode\SymbolLexeme;
use SplFixedArray;

class ParserTest extends TestCase
{

    /**
     * @param array $terminalMap
     * @param array $nonTerminalMap
     * @param int $startSymbolId
     * @param int $eoiTokenId
     * @param array $input
     * @dataProvider providerValidGrammarStrings
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
     * @covers \Remorhaz\UniLex\LL1Parser\Parser
     */
    public function testParse_ValidBuffer_OnLexemeTriggeredForEachToken(
        array $terminalMap,
        array $nonTerminalMap,
        int $startSymbolId,
        int $eoiTokenId,
        array $input
    ): void {
        $grammar = new ContextFreeGrammar($terminalMap, $nonTerminalMap, $startSymbolId, $eoiTokenId);
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

    public function providerValidGrammarStrings(): array
    {
        $examples = new ExampleGrammar;
        $data = [];
        $inputList = [
            [5, 2, 5, 1, 5],
        ];
        foreach ($inputList as $input) {
            $data["Classic example 4.14 from Dragonbook: {$input}"] =
                array_merge(
                    $examples->getDragonBook414Grammar(),
                    [$input]
                );
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
