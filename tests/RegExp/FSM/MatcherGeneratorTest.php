<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\RegExp\FSM\DfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\MatcherGenerator;
use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\TokenFactoryInterface;
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
        $closure = function (CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory) use ($code) {
            return eval($code);
        };
        $tokenFactory = $this->createTokenFactory();
        $matchingBuffer = CharBufferFactory::createFromUtf8String("aabb");
        $actualValue = $closure($matchingBuffer, $tokenFactory);
        self::assertTrue($actualValue);
        $notMatchingBuffer = CharBufferFactory::createFromUtf8String("bbbb");
        $actualValue = $closure($notMatchingBuffer, $tokenFactory);
        self::assertFalse($actualValue);
    }

    private function createTokenFactory(): TokenFactoryInterface
    {
        return new class implements TokenFactoryInterface
        {

            public function createToken(int $tokenId): Token
            {
                return new Token($tokenId, false);
            }

            public function createEoiToken(): Token
            {
                return new Token(0xFF, true);
            }
        };
    }
}
