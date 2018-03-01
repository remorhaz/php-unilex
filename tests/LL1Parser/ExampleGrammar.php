<?php

namespace Remorhaz\UniLex\Test\LL1Parser;

class ExampleGrammar
{
    /**
     * Classic example 4.14 from Dragonbook.
     *
     * E  -> T E'
     * E' -> + T E' | ε
     * T  -> F T'
     * T' -> * F T' | ε
     * F  -> (E) | id
     *
     * Terminals encoded as:
     * +:  1
     * *:  2
     * (:  3
     * ):  4
     * id: 5
     * $:  6
     *
     * Non-terminals encoded as:
     * E:  7
     * E': 8
     * T:  9
     * T': 10
     * F:  11
     *
     * @return array
     */
    public function getDragonBook414Grammar(): array
    {
        $terminalMap = [
            1 => [1],
            2 => [2],
            3 => [3],
            4 => [4],
            5 => [5],
            6 => [6],
        ];

        $nonTerminalMap = [
            7   => [[9, 8]],
            8   => [[1, 9, 8], []],
            9   => [[11, 10]],
            10  => [[2, 11, 10], []],
            11  => [[3, 7, 4], [5]],
        ];

        $startSymbolId = 7;
        $eofTokenId = 6;

        return [$terminalMap, $nonTerminalMap, $startSymbolId, $eofTokenId];
    }

    public function getDragonBook414Firsts(): array
    {
        return [
            "+"     => [1, [1]],
            "*"     => [2, [2]],
            "("     => [3, [3]],
            ")"     => [4, [4]],
            "id"    => [5, [5]],
            "\$"    => [6, [6]],
            "F"     => [11, [3, 5]],
            "T"     => [9, [3, 5]],
            "E"     => [7, [3, 5]],
            "E'"    => [8, [1]],
            "T'"    => [10, [2]],
        ];
    }

    public function getDragonBook414Epsilons(): array
    {
        return [
            "+"     => [1, false],
            "*"     => [2, false],
            "("     => [3, false],
            ")"     => [4, false],
            "id"    => [5, false],
            "\$"    => [6, false],
            "F"     => [11, false],
            "T"     => [9, false],
            "E"     => [7, false],
            "E'"    => [8, true],
            "T'"    => [10, true],
        ];
    }

    public function getDragonBook414Follows(): array
    {
        return [
            "E"     => [7, [4, 6]],
            "E'"    => [8, [4, 6]],
            "T"     => [9, [1, 4, 6]],
            "T'"    => [10, [1, 4, 6]],
            "F"     => [11, [1, 2, 4, 6]],
        ];
    }

    public function getDragonBook414Table(): array
    {
        return [
            7 => [
                3 => [9, 8],
                5 => [9, 8],
            ],
            8 => [
                1 => [1, 9, 8],
                4 => [],
                6 => [],
            ],
            9 => [
                3 => [11, 10],
                5 => [11, 10],
            ],
            10 => [
                1 => [],
                2 => [2, 11, 10],
                4 => [],
                6 => [],
            ],
            11 => [
                3 => [3, 7, 4],
                5 => [5],
            ],
        ];
    }
}
