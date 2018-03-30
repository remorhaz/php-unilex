<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\RegExp\FSM\NfaBuilder;
use Remorhaz\UniLex\RegExp\ParserFactory;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\NfaBuilder
 */
class ParsedFsmTest extends TestCase
{

    /**
     * @dataProvider providerRegExpStateMaps
     * @param string $text
     * @param array $expectedRangeTransitionList
     * @param array $expectedEpsilonTransitionList
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testStateMapBuilder_ValidAST_BuildsMatchingStateMap(
        string $text,
        array $expectedRangeTransitionList,
        array $expectedEpsilonTransitionList
    ): void {
        $buffer = CharBufferFactory::createFromUtf8String($text);
        $tree = new Tree;
        ParserFactory::createFromBuffer($tree, $buffer)->run();
        $stateMap = NfaBuilder::fromTree($tree);
        self::assertEquals($expectedRangeTransitionList, $stateMap->getCharTransitionList());
        self::assertEquals($expectedEpsilonTransitionList, $stateMap->getEpsilonTransitionList());
    }

    public function providerRegExpStateMaps(): array
    {
        $data = [];

        $rangeTransitionList = [];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][2] = true;
        $data["Empty string"] = ['', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [[0x61, 0x61]];
        $epsilonTransitionList = [];
        $data["Single symbol"] = ['a', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [[0x00, 0x10FFFF]];
        $epsilonTransitionList = [];
        $data["Single dot"] = ['.', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[3][4] = [[0x61, 0x61]];
        $rangeTransitionList[5][6] = [[0x62, 0x62]];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][3] = true;
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[1][5] = true;
        $epsilonTransitionList[6][2] = true;
        $data["Alternative of two symbols"] = ['a|b', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[3][4] = [[0x61, 0x61]];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][3] = true;
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[1][5] = true;
        $epsilonTransitionList[5][6] = true;
        $epsilonTransitionList[6][2] = true;
        $data["Alternative of symbol and empty string"] = ['a|', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[5][6] = [[0x61, 0x61]];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][3] = true;
        $epsilonTransitionList[3][4] = true;
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[1][5] = true;
        $epsilonTransitionList[6][2] = true;
        $epsilonTransitionList[1][7] = true;
        $epsilonTransitionList[7][8] = true;
        $epsilonTransitionList[8][2] = true;
        $data["Alternative of symbol and two empty strings"] = ['|a|', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][3] = [[0x61, 0x61]];
        $rangeTransitionList[3][2] = [[0x62, 0x62]];
        $epsilonTransitionList = [];
        $data["Concatenation of two symbols"] = ['ab', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][3] = [[0x61, 0x61]];
        $rangeTransitionList[5][6] = [[0x62, 0x62]];
        $rangeTransitionList[7][8] = [[0x63, 0x63]];
        $rangeTransitionList[4][2] = [[0x64, 0x64]];
        $epsilonTransitionList = [];
        $epsilonTransitionList[3][5] = true;
        $epsilonTransitionList[6][4] = true;
        $epsilonTransitionList[3][7] = true;
        $epsilonTransitionList[8][4] = true;
        $data["Concatenation of two symbols and grouped alternative of two symbols"] =
            ['a(b|c)d', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][3] = [[0x61, 0x61]];
        $rangeTransitionList[3][4] = [[0x61, 0x61]];
        $rangeTransitionList[4][5] = [[0x61, 0x61]];
        $rangeTransitionList[5][6] = [[0x61, 0x61]];
        $rangeTransitionList[6][2] = [[0x61, 0x61]];
        $epsilonTransitionList = [];
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[5][2] = true;
        $epsilonTransitionList[6][2] = true;
        $data["Symbol with finite limit"] = ['a{2,5}', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [[0x61, 0x61]];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][2] = true;
        $data["Optional symbol"] = ['a?', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][3] = [[0x61, 0x61]];
        $rangeTransitionList[3][4] = [[0x61, 0x61]];
        $rangeTransitionList[5][6] = [[0x61, 0x61]];
        $epsilonTransitionList = [];
        $epsilonTransitionList[4][5] = true;
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[6][2] = true;
        $epsilonTransitionList[6][5] = true;
        $data["Symbol with infinite limit"] = ['a{2,}', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[3][4] = [[0x61, 0x61]];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][3] = true;
        $epsilonTransitionList[1][2] = true;
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[4][3] = true;
        $data["Kleene star applied to symbol"] = ['a*', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [[0x61, 0x61]];
        $epsilonTransitionList = [];
        $data["Escaped Unicode symbol"] = ['\\u0061', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [[0x7F, 0x7F]];
        $epsilonTransitionList = [];
        $data["Escaped Unicode symbol"] = ['\\c?', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [[0x07, 0x07]];
        $epsilonTransitionList = [];
        $data["Escaped non-printable symbol"] = ['\\a', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [[0x46, 0x46]];
        $epsilonTransitionList = [];
        $data["Escaped arbitrary symbol"] = ['\\F', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [[0x61, 0x62]];
        $epsilonTransitionList = [];
        $data["Two neighbour symbols in a class"] = ['[ab]', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [[0x61, 0x61]];
        $epsilonTransitionList = [];
        $data["Two equal symbols in a class"] = ['[aa]', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [[0x61, 0x61], [0x63, 0x63]];
        $epsilonTransitionList = [];
        $data["Two not neighbour symbols in a class in inverted order"] =
            ['[ca]', $rangeTransitionList, $epsilonTransitionList];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [[0x00, 0x60], [0x62, 0x62], [0x64, 0x10FFFF]];
        $epsilonTransitionList = [];
        $data["Two symbols in inverted class"] = ['[^ac]', $rangeTransitionList, $epsilonTransitionList];

        return $data;
    }
}
