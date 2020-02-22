<?php

namespace Remorhaz\UniLex\Example\Test\Brainfuck;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\Brainfuck\Exception as BrainfuckException;
use Remorhaz\UniLex\Example\Brainfuck\Interpreter;
use Remorhaz\UniLex\Exception as UniLexException;

/**
 * @covers \Remorhaz\UniLex\Example\Brainfuck\Interpreter
 */
class InterpreterTest extends TestCase
{

    /**
     * @throws BrainfuckException
     * @throws UniLexException
     */
    public function testExec_ValidInput_GetOutputReturnsMatchingValue(): void
    {
        $code =
            "++++++++++[>+++++++>++++++++++>+++>+<<<<-]>++" .
            ".>+.+++++++..+++.>++.<<+++++++++++++++.>.+++." .
            "------.--------.>+.>.";
        $interpreter = new Interpreter;
        $interpreter->exec($code);
        $actualValue = $interpreter->getOutput();
        self::assertSame("Hello World!\n", $actualValue);
    }

    /**
     * @throws BrainfuckException
     */
    public function testGetOutput_NoExecCalled_ThrowsException(): void
    {
        $interpreter = new Interpreter;

        $this->expectException(BrainfuckException::class);
        $this->expectExceptionMessage('Output is not defined');
        $interpreter->getOutput();
    }
}
