<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\RegExp\FSM\DfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\MatcherGenerator;
use Remorhaz\UniLex\RegExp\ParserFactory;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

/**
 * @coversNothing
 */
class MatcherGeneratorTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGenerate_ValidDfa_BuildsValidMatcher(): void
    {
        $buffer = CharBufferFactory::createFromUtf8String("(a|b)abb");
        $dfa = DfaBuilder::fromBuffer($buffer);
        $generator = new MatcherGenerator($dfa);
        $generator->generate();
        $code = $generator->getOutput();
        $closure = function (CharBufferInterface $buffer) use ($code) {
            return eval($code);
        };
        $actualValue = $closure(CharBufferFactory::createFromUtf8String("aabb"));
        self::assertTrue($actualValue);
        $actualValue = $closure(CharBufferFactory::createFromUtf8String("bbbb"));
        self::assertFalse($actualValue);
    }
}
