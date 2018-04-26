<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\RegExp\FSM\NfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;
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
     * @param array $expectedSymbolTransitionList
     * @param array $expectedEpsilonTransitionList
     * @param array $expectedSymbolTable
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testStateMapBuilder_ValidAST_BuildsMatchingStateMap(
        string $text,
        array $expectedSymbolTransitionList,
        array $expectedEpsilonTransitionList,
        array $expectedSymbolTable
    ): void {
        $buffer = CharBufferFactory::createFromString($text);
        $tree = new Tree;
        ParserFactory::createFromBuffer($tree, $buffer)->run();
        $nfa = NfaBuilder::fromTree($tree);
        $actualSymbolTransitionList = $nfa->getSymbolTransitionMap()->getTransitionList();
        self::assertEquals($expectedSymbolTransitionList, $actualSymbolTransitionList);
        $actualEpsilonTransitionList = $nfa->getEpsilonTransitionMap()->getTransitionList();
        self::assertEquals($expectedEpsilonTransitionList, $actualEpsilonTransitionList);
        $actualSymbolTable = $this->exportRangeSetList($nfa->getSymbolTable()->getRangeSetList());
        self::assertEquals($expectedSymbolTable, $actualSymbolTable);
    }

    public function providerRegExpStateMaps(): array
    {
        $data = [];

        $rangeTransitionList = [];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][2] = true;
        $symbolTable = [];
        $data["Empty string"] = ['', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        $data["Single symbol"] = ['a', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x00, 0x10FFFF]];
        $data["Single dot"] = ['.', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

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
        $data["Alternative of two symbols"] = ['a|b', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

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
        $data["Alternative of symbol and empty string"] =
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
        $data["Alternative of symbol and two empty strings"] =
            ['|a|', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][3] = [0];
        $rangeTransitionList[3][2] = [1];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        $symbolTable[1] = [[0x62, 0x62]];
        $data["Concatenation of two symbols"] = ['ab', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][3] = [0];
        $rangeTransitionList[3][4] = [1];
        $rangeTransitionList[4][2] = [1];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        $symbolTable[1] = [[0x62, 0x62]];
        $data["Concatenation of symbol and repeated symbol"] =
            ['ab{2}', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $data["Single zero repeat"] = ['a{0}', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

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
        $data["Concatenation of two symbols and grouped alternative of two symbols"] =
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
        $data["Symbol with finite limit"] = ['a{2,5}', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][2] = true;
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        $data["Optional symbol"] = ['a?', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

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
        $data["Symbol with infinite limit"] = ['a{2,}', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[3][4] = [0];
        $epsilonTransitionList = [];
        $epsilonTransitionList[1][3] = true;
        $epsilonTransitionList[1][2] = true;
        $epsilonTransitionList[4][2] = true;
        $epsilonTransitionList[4][3] = true;
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        $data["Kleene star applied to symbol"] = ['a*', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        $data["Escaped Unicode symbol"] = ['\\u0061', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x7F, 0x7F]];
        $data["Escaped Unicode symbol"] = ['\\c?', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x07, 0x07]];
        $data["Escaped non-printable symbol"] = ['\\a', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x46, 0x46]];
        $data["Escaped arbitrary symbol"] = ['\\F', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x2E, 0x2E]];
        $data["Escaped meta-symbol"] = ['\\.', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x62]];
        $data["Two neighbour symbols in a class"] =
            ['[ab]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61]];
        $data["Two equal symbols in a class"] = ['[aa]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x61], [0x63, 0x63]];
        $data["Two not neighbour symbols in a class in inverted order"] =
            ['[ca]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x00, 0x60], [0x62, 0x62], [0x64, 0x10FFFF]];
        $data["Two symbols in inverted class"] = ['[^ac]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x61, 0x63]];
        $data["Single range between symbols in class"] =
            ['[a-c]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x46, 0x4B]];
        $data["Single range between ordinary escape and symbol in class"] =
            ['[\\F-K]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x7F, 0x0429]];
        $data["Single range between control and Unicode escapes in class"] =
            ['[\\c?-\\u0429]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x0410, 0x0429]];
        $data["Single range between ordinary non-ASCII escape and raw non-ASCII symbol in class"] =
            ['[\\А-Щ]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        $rangeTransitionList = [];
        $rangeTransitionList[1][2] = [0];
        $epsilonTransitionList = [];
        $symbolTable = [];
        $symbolTable[0] = [[0x09, 0x7A]];
        $data["Two intersecting ranges in class"] =
            ['[\\t-aA-z]', $rangeTransitionList, $epsilonTransitionList, $symbolTable];

        return $data;
    }

    /**
     * @param RangeSet[] $rangeSetList
     * @return array
     */
    private function exportRangeSetList(array $rangeSetList): array
    {
        $result = [];
        foreach ($rangeSetList as $symbolId => $rangeSet) {
            $result[$symbolId] = $rangeSet->export();
        }
        return $result;
    }
}
