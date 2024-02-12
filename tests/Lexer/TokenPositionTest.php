<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Lexer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Lexer\TokenPosition;

#[CoversClass(TokenPosition::class)]
class TokenPositionTest extends TestCase
{
    /**
     * @throws UniLexException
     */
    public function testGetStartOffset_ConstructWithValue_ReturnsSameValue(): void
    {
        $position = new TokenPosition(0, 1);
        $actualValue = $position->getStartOffset();
        self::assertSame(0, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetFinishOffset_ConstructWithValue_ReturnsSameValue(): void
    {
        $position = new TokenPosition(0, 1);
        $actualValue = $position->getFinishOffset();
        self::assertSame(1, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    #[DataProvider('providerOffsetsWithLength')]
    public function testGetLength_Constructed_ReturnsCorrectSize(
        int $startOffset,
        int $finishOffset,
        int $expectedLength,
    ): void {
        $position = new TokenPosition($startOffset, $finishOffset);
        $actualValue = $position->getLength();
        self::assertSame($expectedLength, $actualValue);
    }

    /**
     * @return iterable<string, array{int, int, int}>
     */
    public static function providerOffsetsWithLength(): iterable
    {
        return [
            'Single symbol token' => [0, 1, 1],
            'Empty token' => [0, 0, 0],
        ];
    }

    /**
     * @throws UniLexException
     */
    public function testConstruct_NegativeStartOffset_ThrowsException()
    {
        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Negative start offset in token position: -1');
        new TokenPosition(-1, 1);
    }

    /**
     * @throws UniLexException
     */
    public function testConstruct_FinishOffsetLessThanStart_ThrowsException()
    {
        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Finish offset lesser than start in token position: -1 < 0');
        new TokenPosition(0, -1);
    }
}
