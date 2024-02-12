<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\IntRangeSets\RangeInterface;
use Remorhaz\IntRangeSets\RangeSetInterface;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\RegExp\FSM\NfaBuilder;
use Remorhaz\UniLex\RegExp\ParserFactory;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

use function array_map;

#[CoversClass(NfaBuilder::class)]
class ParsedFsmTest extends TestCase
{
    /**
     * @throws UniLexException
     */
    #[DataProvider('providerRegExpStateMaps')]
    public function testStateMapBuilder_ValidAST_BuildsMatchingStateMap(
        string $text,
        array $expectedSymbolTransitionList,
        array $expectedEpsilonTransitionList,
        array $expectedSymbolTable,
    ): void {
        $buffer = CharBufferFactory::createFromString($text);
        $tree = new Tree();
        ParserFactory::createFromBuffer($tree, $buffer)->run();
        $nfa = NfaBuilder::fromTree($tree);
        $actualSymbolTransitionList = $nfa->getSymbolTransitionMap()->getTransitionList();
        self::assertEquals($expectedSymbolTransitionList, $actualSymbolTransitionList);
        $actualEpsilonTransitionList = $nfa->getEpsilonTransitionMap()->getTransitionList();
        self::assertEquals($expectedEpsilonTransitionList, $actualEpsilonTransitionList);
        $actualSymbolTable = $this->exportRangeSetList($nfa->getSymbolTable()->getRangeSetList());
        self::assertEquals($expectedSymbolTable, $actualSymbolTable);
    }

    /**
     * @return iterable<string, array{string, array, array, array}>
     */
    public static function providerRegExpStateMaps(): iterable
    {
        $rangeTransitionList = [];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][2] = true;
        $symbolTable = [];
        yield "Empty string" => ['', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        yield "Single symbol" => ['a', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x00, 0x10FFFF]];
        yield "Single dot" => ['.', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[3][4] = [0];
        $rangeTransitionList[5][6] = [1];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][3] = true;
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[1][5] = true;
        $epsilonTransitionList[6][2] = true;
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        $symbolTable[1] = [[0x62, 0x62]];
        yield "Alternative of two symbols" => ['a|b', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[3][4] = [0];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][3] = true;
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[1][5] = true;
        $epsilonTransitionList[5][6] = true;
        $epsilonTransitionList[6][2] = true;
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        yield "Alternative of symbol and empty string" =>
            ['a|', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[5][6] = [0];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][3] = true;
        $epsilonTransitionList[3][4] = true;
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[1][5] = true;
        $epsilonTransitionList[6][2] = true;
        $epsilonTransitionList[1][7] = true;
        $epsilonTransitionList[7][8] = true;
        $epsilonTransitionList[8][2] = true;
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        yield "Alternative of symbol and two empty strings" =>
            ['|a|', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][3] = [0];
        $rangeTransitionList[3][2] = [1];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        $symbolTable[1] = [[0x62, 0x62]];
        yield "Concatenation of two symbols" => ['ab', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][3] = [0];
        $rangeTransitionList[3][4] = [1];
        $rangeTransitionList[4][2] = [1];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        $symbolTable[1] = [[0x62, 0x62]];
        yield "Concatenation of symbol and repeated symbol" =>
            ['ab{2}', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $epsilonTransitionList = [];
        $symbolTable = [];
        yield "Single zero repeat" => ['a{0}', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][3] = [0];
        $rangeTransitionList[5][6] = [1];
        $rangeTransitionList[7][8] = [2];
        $rangeTransitionList[4][2] = [3];
        $epsilonTransitionList = [];
        $epsilonTransitionList[3][5] = true;
        $epsilonTransitionList[6][4] = true;
        $epsilonTransitionList[3][7] = true;
        $epsilonTransitionList[8][4] = true;
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        $symbolTable[1] = [[0x62, 0x62]];
        $symbolTable[2] = [[0x63, 0x63]];
        $symbolTable[3] = [[0x64, 0x64]];
        yield "Concatenation of two symbols and grouped alternative of two symbols" =>
            ['a(b|c)d', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][3] = [0];
        $rangeTransitionList[3][4] = [0];
        $rangeTransitionList[4][5] = [0];
        $rangeTransitionList[5][6] = [0];
        $rangeTransitionList[6][2] = [0];
        $epsilonTransitionList = [];
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[5][2] = true;
        $epsilonTransitionList[6][2] = true;
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        yield "Symbol with finite limit" => ['a{2,5}', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][2] = true;
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        yield "Optional symbol" => ['a?', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][3] = [0];
        $rangeTransitionList[3][4] = [0];
        $rangeTransitionList[5][6] = [0];
        $epsilonTransitionList = [];
        $epsilonTransitionList[4][5] = true;
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[6][2] = true;
        $epsilonTransitionList[6][5] = true;
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        yield "Symbol with infinite limit" => ['a{2,}', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[3][4] = [0];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][3] = true;
        $epsilonTransitionList[1][2] = true;
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[4][3] = true;
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        yield "Kleene star applied to symbol" => ['a*', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        yield "Escaped Unicode symbol" => ['\\u0061', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x7F, 0x7F]];
        yield "Escaped control symbol" => ['\\c?', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x07, 0x07]];
        yield "Escaped non-printable symbol" => ['\\a', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x46, 0x46]];
        yield "Escaped arbitrary symbol" => ['\\F', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x2E, 0x2E]];
        yield "Escaped meta-symbol" => ['\\.', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x62]];
        yield "Two neighbour symbols in a class" =>
            ['[ab]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        yield "Two equal symbols in a class" => ['[aa]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61], [0x63, 0x63]];
        yield "Two not neighbour symbols in a class in inverted order" =>
            ['[ca]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x00, 0x60], [0x62, 0x62], [0x64, 0x10FFFF]];
        yield "Two symbols in inverted class" => ['[^ac]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x63]];
        yield "Single range between symbols in class" =>
            ['[a-c]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x46, 0x4B]];
        yield "Single range between ordinary escape and symbol in class" =>
            ['[\\F-K]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x7F, 0x0429]];
        yield "Single range between control and Unicode escapes in class" =>
            ['[\\c?-\\u0429]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x0410, 0x0429]];
        yield "Single range between ordinary non-ASCII escape and raw non-ASCII symbol in class" =>
            ['[\\А-Щ]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x09, 0x7A]];
        yield "Two intersecting ranges in class" =>
            ['[\\t-aA-z]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[3][4] = [0];
        $rangeTransitionList[5][7] = [1];
        $rangeTransitionList[7][6] = [0, 2];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][3] = true;
        $epsilonTransitionList[1][5] = true;
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[6][2] = true;
        $symbolTable = [];
        $symbolTable[0] = [[0x35, 0x35]];
        $symbolTable[1] = [[0x61, 0x61]];
        $symbolTable[2] = [[0x30, 0x34], [0x36, 0x39]];
        yield "One alternative is a part of another's class" =>
            ['5|a[0-9]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];
    }

    /**
     * @param array<int, RangeSetInterface> $rangeSetList
     * @return array<int, list<array{int, int}>>
     */
    private function exportRangeSetList(array $rangeSetList): array
    {
        $result = [];
        foreach ($rangeSetList as $symbolId => $rangeSet) {
            $result[$symbolId] = array_map(
                fn (RangeInterface $range): array => [$range->getStart(), $range->getFinish()],
                $rangeSet->getRanges(),
            );
        }
        return $result;
    }
}
