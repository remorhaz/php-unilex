<?php

namespace Remorhaz\UniLex\Test\Lexer;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFree\Grammar;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Lexer\TokenMatcherGenerator;
use Remorhaz\UniLex\Lexer\TokenMatcherInterface;
use Remorhaz\UniLex\Lexer\TokenMatcherSpec;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\Lexer\TokenSpec;
use Remorhaz\UniLex\Lexer\TokenMatcherTemplate;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

/**
 * @covers \Remorhaz\UniLex\Lexer\TokenMatcherGenerator
 */
class TokenMatcherGeneratorTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testLoad_EmptySpec_ResultMatchReturnsFalse(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $matcher = (new TokenMatcherGenerator($spec))->load();
        $grammar = new Grammar(0, 1, 2);
        $buffer = CharBufferFactory::createFromString("a");
        $actualValue = $matcher->match($buffer, new TokenFactory($grammar));
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testLoad_ValidSpecAndBuffer_ResultMatchReturnsTrue(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $tokenSpec = new TokenSpec("a", "");
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_CONTEXT, $tokenSpec);
        $matcher = (new TokenMatcherGenerator($spec))->load();
        $grammar = new Grammar(0, 1, 2);
        $buffer = CharBufferFactory::createFromString("ab");
        $actualValue = $matcher->match($buffer, new TokenFactory($grammar));
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testLoad_ValidSpecAndBuffer_ResultGetTokenReturnsTokenWithMatchingTypeAfterMatchCalled(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $spec->setOnToken("\$context->setNewToken(1);");
        $tokenSpec = new TokenSpec("a", "");
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_CONTEXT, $tokenSpec);
        $matcher = (new TokenMatcherGenerator($spec))->load();
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(1, 1);
        $grammar->addToken(2, 2);
        $buffer = CharBufferFactory::createFromString("ab");
        $matcher->match($buffer, new TokenFactory($grammar));
        $token = $matcher->getToken();
        self::assertSame(1, $token->getType());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testLoad_ValidSpecNotMatchingBuffer_ResultMatchReturnsFalse(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $spec->setOnToken("\$context->setNewToken(1);");
        $tokenSpec = new TokenSpec("a", "");
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_CONTEXT, $tokenSpec);
        $matcher = (new TokenMatcherGenerator($spec))->load();
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(1, 1);
        $grammar->addToken(2, 2);
        $buffer = CharBufferFactory::createFromString("ba");
        $actualValue = $matcher->match($buffer, new TokenFactory($grammar));
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Token is not defined
     */
    public function testLoad_ValidSpecWrongBuffer_ResultGetTokenThrowsException(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $spec->setOnToken("\$this->token = \$tokenFactory->createToken(\$tokenType);");
        $tokenSpec = new TokenSpec("a", "");
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_CONTEXT, $tokenSpec);
        $matcher = (new TokenMatcherGenerator($spec))->load();
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(1, 1);
        $grammar->addToken(2, 2);
        $buffer = CharBufferFactory::createFromString("ba");
        $matcher->match($buffer, new TokenFactory($grammar));
        $matcher->getToken();
    }

    /**
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetOutput_SpecWithFileComment_ResultContainsValueInComment(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $comment = md5($matcherClass);
        $spec->addFileComment($comment);
        $output = (new TokenMatcherGenerator($spec))->getOutput();
        $escapedComment = preg_quote($comment, '/');
        self::assertRegExp("/^\\s\\*\\s{$escapedComment}$/m", $output);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid PHP code generated
     */
    public function testLoad_InvalidOutput_ThrowsException(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $generator = $this
            ->getMockBuilder(TokenMatcherGenerator::class)
            ->setConstructorArgs([$spec])
            ->setMethods(['getOutput'])
            ->getMock();
        $generator
            ->method('getOutput')
            ->willReturn("<?php invalid:::php");

        /** @var TokenMatcherGenerator $generator */
        $generator->load();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Failed to generate target class
     */
    public function testLoad_EmptyOutput_ThrowsException(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $generator = $this
            ->getMockBuilder(TokenMatcherGenerator::class)
            ->setConstructorArgs([$spec])
            ->setMethods(['getOutput'])
            ->getMock();
        $generator
            ->method('getOutput')
            ->willReturn("");

        /** @var TokenMatcherGenerator $generator */
        $generator->load();
    }

    /**
     * @param string $text
     * @param string $regExp
     * @param string $expectedValue
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidRegExpInput
     */
    public function testLoad_ValidInput_MatchesValidToken(string $text, string $regExp, string $expectedValue): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $code = <<<SOURCE
\$context
    ->setNewToken(0)
    ->setTokenAttribute('text', \$context->getSymbolString());
SOURCE;
        $tokenSpec = new TokenSpec($regExp, $code);
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_CONTEXT, $tokenSpec);
        $generator = new TokenMatcherGenerator($spec);
        $buffer = CharBufferFactory::createFromString($text);
        $lexer = new TokenReader($buffer, $generator->load(), new \Remorhaz\UniLex\Lexer\TokenFactory(0xFF));
        $actualValue = $lexer
            ->read()
            ->getAttribute('text');
        self::assertSame($expectedValue, $actualValue);
    }

    public function providerValidRegExpInput(): array
    {
        return [
            "Single latin char" => ['ab', 'a', 'a'],
            "Zero or many latin char" => ['aabc', 'a*', 'aa'],
            "Number without leading zero" => ['103abc', '[1-9][0-9]*', '103'],
        ];
    }

    /**
     * @param string $text
     * @param string $prefixRegExp
     * @param string $regExp
     * @param string $expectedValue
     * @dataProvider providerValidRegExpInputWithPrefix
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testLoad_ValidInputWithPrefix_MatchesValidToken(
        string $text,
        string $prefixRegExp,
        string $regExp,
        string $expectedValue
    ): void {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $code = <<<SOURCE
\$context
    ->setNewToken(0)
    ->setTokenAttribute('text', \$context->getSymbolString());
SOURCE;
        $tokenSpec = new TokenSpec($regExp, $code);
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_CONTEXT, $tokenSpec);
        $prefixTokenSpec = new TokenSpec($prefixRegExp, $code);
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_CONTEXT, $prefixTokenSpec);
        $generator = new TokenMatcherGenerator($spec);
        $buffer = CharBufferFactory::createFromString($text);
        $lexer = new TokenReader($buffer, $generator->load(), new \Remorhaz\UniLex\Lexer\TokenFactory(0xFF));
        $lexer->read();
        $actualValue = $lexer
            ->read()
            ->getAttribute('text');
        self::assertSame($expectedValue, $actualValue);
    }

    public function providerValidRegExpInputWithPrefix(): array
    {
        return [
            "Two Kleene star patterns" => ['aabbbcccc', 'a*', 'b*', 'bbb'],
        ];
    }

    private function createTokenMatcherClassName(): string
    {
        static $nextMatcherClassIndex = 1;
        return __CLASS__ . '\TokenMatcher' . (string) $nextMatcherClassIndex++;
    }
}
