<?php

namespace Remorhaz\UniLex\Example\Test\Brainfuck;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\Brainfuck\Interpreter;

class InterpreterTest extends TestCase
{

    public function testRun_ValidInput_MatchingOutput(): void
    {
        $input =
            "++++++++++[>+++++++>++++++++++>+++>+<<<<-]>++" .
            ".>+.+++++++..+++.>++.<<+++++++++++++++.>.+++." .
            "------.--------.>+.>.";
        $interpreter = new Interpreter;
        $interpreter->exec($input);
    }
}
