<?php

namespace Remorhaz\UniLex\Test\Unicode;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\Unicode\LexemeFactory;
use Remorhaz\UniLex\LexemeFactoryInterface;
use Remorhaz\UniLex\LexemePosition;
use Remorhaz\UniLex\SymbolBufferInterface;
use Remorhaz\UniLex\Unicode\LexemeInfo;
use Remorhaz\UniLex\Unicode\LexemeMatcherInterface;
use Remorhaz\UniLex\Unicode\SymbolBuffer;
use Remorhaz\UniLex\Unicode\Utf8LexemeMatcher;
use SplFixedArray;

/**
 * @covers \Remorhaz\UniLex\Unicode\SymbolBuffer
 */
class SymbolBufferTest extends TestCase
{

    public function testIsEnd_EmptySourceBuffer_ReturnsTrue(): void
    {
        $actualValue = $this
            ->createSymbolBuffer('')
            ->isEnd();
        self::assertTrue($actualValue);
    }

    public function testIsEnd_NotEmptySourceBuffer_ReturnsFalse(): void
    {
        $actualValue = $this
            ->createSymbolBuffer('a')
            ->isEnd();
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testIsEnd_NextSymbolCalledAtLastSymbol_ReturnsTrue(): void
    {
        $buffer = $this->createSymbolBuffer('a');
        $buffer->nextSymbol();
        $actualValue = $buffer->isEnd();
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage No symbol to preview at index 0
     */
    public function testGetSymbol_EmptySourceBuffer_ThrowsException(): void
    {
        $this
            ->createSymbolBuffer('')
            ->getSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetSymbol_CalledTwice_ReturnsSameSymbol(): void
    {
        $buffer = $this->createSymbolBuffer('ab');
        $firstSymbol = $buffer->getSymbol();
        $secondSymbol = $buffer->getSymbol();
        self::assertSame($firstSymbol, $secondSymbol);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetSymbol_NotEmptySourceBuffer_ReturnsMatchingLexeme(): void
    {
        $actualValue = $this
            ->createSymbolBuffer('a')
            ->getSymbol();
        self::assertSame(0x61, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid lexeme at index 0
     */
    public function testGetSymbol_InvalidMatcher_ThrowsException(): void
    {
        $this
            ->createSymbolBuffer('a', $this->createInvalidLexemeMatcher())
            ->getSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid lexeme at index 0
     */
    public function testNextSymbol_InvalidMatcher_ThrowsException(): void
    {
        $this
            ->createSymbolBuffer('a', $this->createInvalidLexemeMatcher())
            ->nextSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unexpected end of buffer at index 0
     */
    public function testNextSymbol_EmptyBuffer_ThrowsException(): void
    {
        $this
            ->createSymbolBuffer('')
            ->nextSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testNextSymbol_NotBufferEnd_GetSymbolReturnsNextSymbol(): void
    {
        $buffer = $this->createSymbolBuffer('ab');
        $buffer->nextSymbol();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testNextSymbol_ResetLexemeCalled_ReturnsSecondSymbol(): void
    {
        $buffer = $this->createSymbolBuffer('ab');
        $buffer->nextSymbol();
        $buffer->resetLexeme();
        $buffer->nextSymbol();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testResetLexeme_NextSymbolCalled_GetSymbolReturnsFirstSymbol(): void
    {
        $buffer = $this->createSymbolBuffer('ab');
        $buffer->nextSymbol();
        $buffer->resetLexeme();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x61, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testFinishLexeme_NextSymbolCalled_GetSymbolReturnsNextSymbol(): void
    {
        $buffer = $this->createSymbolBuffer('ab');
        $buffer->nextSymbol();
        $buffer->finishLexeme();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testExtractLexeme_ValidPosition_ReturnsMatchingBuffer(): void
    {
        $position = new LexemePosition(0, 1);
        $expectedValue = SplFixedArray::fromArray([0x61]);
        $buffer = $this->createSymbolBuffer('a');
        $buffer->nextSymbol();
        $actualValue = $buffer->extractLexeme($position);
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @param string $text
     * @param int $nextSymbolCount Times to call nextSymbol().
     * @param int $startOffset
     * @param int $finishOffset
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidEmptyPosition
     */
    public function testExtractLexeme_ValidEmptyPosition_ReturnsEmptyBuffer(
        string $text,
        int $nextSymbolCount,
        int $startOffset,
        int $finishOffset
    ): void {
        $position = new LexemePosition($startOffset, $finishOffset);
        $expectedValue = new SplFixedArray;
        $buffer = $this->createSymbolBuffer($text);
        while ($nextSymbolCount-- > 0) {
            $buffer->nextSymbol();
        }
        $actualValue = $buffer->extractLexeme($position);
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @param string $text
     * @param int $nextSymbolCount Times to call nextSymbol().
     * @param int $startOffset
     * @param int $finishOffset
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidEmptyPosition
     */
    public function testGetLexemeInfo_ValidEmptyPosition_ReturnsLexemeInfoSamePosition(
        string $text,
        int $nextSymbolCount,
        int $startOffset,
        int $finishOffset
    ): void {
        $buffer = $this->createSymbolBuffer($text);
        while ($nextSymbolCount-- > 0) {
            $buffer->nextSymbol();
        }
        $buffer->finishLexeme();
        $expectedValue = new LexemePosition($startOffset, $finishOffset);
        $actualValue = $buffer
            ->getLexemeInfo()
            ->getPosition();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @param string $text
     * @param int $offset
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidEmptyPosition
     */
    public function testGetLexemeInfo_ValidEmptyPosition_LexemeInfoExtractsEmptyBuffer(string $text, int $offset): void
    {
        $buffer = $this->createSymbolBufferWithLexeme($text, $offset, 0);
        $expectedValue = new SplFixedArray;
        $actualValue = $buffer
            ->getLexemeInfo()
            ->extract();
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerValidEmptyPosition(): array
    {
        return [
            'Non-empty buffer start' => ['a', 0, 0, 0],
            'Non-empty buffer finish' => ['a', 1, 1, 1],
            'Empty buffer start/finish' => ['', 0, 0, 0],
        ];
    }

    /**
     * @param string $text
     * @param int $offset
     * @param int $length
     * @param array $expectedBuffer
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidLexemeBuffer
     */
    public function testGetLexemeInfo_ValidLexeme_LexemeInfoExtractsMatchingBuffer(
        string $text,
        int $offset,
        int $length,
        array $expectedBuffer
    ): void {
        $buffer = $this->createSymbolBufferWithLexeme($text, $offset, $length);
        $expectedValue = SplFixedArray::fromArray($expectedBuffer);
        $actualValue = $buffer
            ->getLexemeInfo()
            ->extract();
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerValidLexemeBuffer(): array
    {
        return [
            'Single latin char at start' => ['a', 0, 1, [0x61]],
            'Two cyrillic chars at end' => ['абв', 1, 2, [0x0431, 0x0432]],
        ];
    }

    /**
     * @param string $text
     * @param int $offset
     * @param int $length
     * @param int $positionStartOffset
     * @param int $positionFinishOffset
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidLexemePosition
     */
    public function testGetLexemeInfo_ValidLexeme_LexemeInfoHasMatchingPosition(
        string $text,
        int $offset,
        int $length,
        int $positionStartOffset,
        int $positionFinishOffset
    ): void {
        $buffer = $this->createSymbolBufferWithLexeme($text, $offset, $length);
        $expectedValue = new LexemePosition($positionStartOffset, $positionFinishOffset);
        $actualValue = $buffer
            ->getLexemeInfo()
            ->getPosition();
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerValidLexemePosition(): array
    {
        return [
            'Single latin char at start' => ['a', 0, 1, 0, 1],
            'Two cyrillic chars at end' => ['абв', 1, 2, 1, 3],
        ];
    }

    /**
     * @param string $text
     * @param int $offset
     * @param int $length
     * @param int $positionStartOffset
     * @param int $positionFinishOffset
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidLexemeSourcePosition
     */
    public function testGetLexemeInfo_ValidLexeme_LexemeInfoHasMatchingSourcePosition(
        string $text,
        int $offset,
        int $length,
        int $positionStartOffset,
        int $positionFinishOffset
    ): void {
        $buffer = $this->createSymbolBufferWithLexeme($text, $offset, $length);
        $expectedValue = new LexemePosition($positionStartOffset, $positionFinishOffset);
        $lexemeInfo = $buffer->getLexemeInfo();
        self::assertInstanceOf(LexemeInfo::class, $lexemeInfo);
        /** @var LexemeInfo $lexemeInfo */
        $actualValue = $lexemeInfo->getSourcePosition();
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerValidLexemeSourcePosition(): array
    {
        return [
            'Single latin char at start' => ['a', 0, 1, 0, 1],
            'Two cyrillic chars at end' => ['абв', 1, 2, 2, 6],
        ];
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     */
    public function testExtractLexeme_InvalidEmptyPosition_ReturnsEmptyBuffer(): void
    {
        $position = new LexemePosition(2, 2);
        $expectedValue = new SplFixedArray;
        $buffer = $this->createSymbolBuffer('a');
        $actualValue = $buffer->extractLexeme($position);
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage No symbol to extract at offset 0
     */
    public function testExtractLexeme_InvalidPosition_ThrowsException(): void
    {
        $position = new LexemePosition(0, 1);
        $this
            ->createSymbolBuffer('a')
            ->extractLexeme($position);
    }

    private function createSymbolBuffer(string $text, LexemeMatcherInterface $matcher = null): SymbolBuffer
    {
        $source = \Remorhaz\UniLex\SymbolBuffer::fromString($text);
        if (!isset($matcher)) {
            $matcher = new Utf8LexemeMatcher;
        }
        $lexemeFactory = new LexemeFactory;
        return new SymbolBuffer($source, $matcher, $lexemeFactory);
    }

    /**
     * @param string $text
     * @param int $offset
     * @param int $length
     * @return SymbolBuffer
     * @throws \Remorhaz\UniLex\Exception
     */
    private function createSymbolBufferWithLexeme(string $text, int $offset, int $length): SymbolBuffer
    {
        $buffer = $this->createSymbolBuffer($text);
        while ($offset-- > 0) {
            $buffer->nextSymbol();
        }
        $buffer->finishLexeme();
        while ($length-- > 0) {
            $buffer->nextSymbol();
        }
        return $buffer;
    }

    private function createInvalidLexemeMatcher(): LexemeMatcherInterface
    {
        return new class implements LexemeMatcherInterface {

            public function match(SymbolBufferInterface $buffer, LexemeFactoryInterface $lexemeFactory): Lexeme
            {
                $buffer->nextSymbol();
                $lexeme = new class(0) extends Lexeme
                {
                };
                return $lexeme;
            }
        };
    }
}
