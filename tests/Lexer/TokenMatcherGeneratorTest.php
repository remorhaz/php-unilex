<?php

namespace Remorhaz\UniLex\Test\Lexer;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Remorhaz\UniLex\Exception as UniLexException;
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
     * @throws UniLexException
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
     * @throws UniLexException
     */
    public function testLoad_ValidSpecAndBuffer_ResultMatchReturnsTrue(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $tokenSpec = new TokenSpec("a", "");
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_MODE, $tokenSpec);
        $matcher = (new TokenMatcherGenerator($spec))->load();
        $grammar = new Grammar(0, 1, 2);
        $buffer = CharBufferFactory::createFromString("ab");
        $actualValue = $matcher->match($buffer, new TokenFactory($grammar));
        self::assertTrue($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testLoad_ValidSpecAndBuffer_ResultGetTokenReturnsTokenWithMatchingTypeAfterMatchCalled(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $spec->setOnToken("\$context->setNewToken(1);");
        $tokenSpec = new TokenSpec("a", "");
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_MODE, $tokenSpec);
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
     * @throws UniLexException
     */
    public function testLoad_ValidSpecNotMatchingBuffer_ResultMatchReturnsFalse(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $spec->setOnToken("\$context->setNewToken(1);");
        $tokenSpec = new TokenSpec("a", "");
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_MODE, $tokenSpec);
        $matcher = (new TokenMatcherGenerator($spec))->load();
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(1, 1);
        $grammar->addToken(2, 2);
        $buffer = CharBufferFactory::createFromString("ba");
        $actualValue = $matcher->match($buffer, new TokenFactory($grammar));
        self::assertFalse($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testLoad_ValidSpecWrongBuffer_ResultGetTokenThrowsException(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $spec->setOnToken("\$this->token = \$tokenFactory->createToken(\$tokenType);");
        $tokenSpec = new TokenSpec("a", "");
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_MODE, $tokenSpec);
        $matcher = (new TokenMatcherGenerator($spec))->load();
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(1, 1);
        $grammar->addToken(2, 2);
        $buffer = CharBufferFactory::createFromString("ba");
        $matcher->match($buffer, new TokenFactory($grammar));

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Token is not defined');
        $matcher->getToken();
    }

    /**
     * @throws ReflectionException
     * @throws UniLexException
     */
    public function testGetOutput_SpecWithFileComment_ResultContainsValueInComment(): void
    {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $comment = md5($matcherClass);
        $spec->addFileComment($comment);
        $output = (new TokenMatcherGenerator($spec))->getOutput();
        $escapedComment = preg_quote($comment, '/');
        self::assertMatchesRegularExpression("/^\\s\\*\\s{$escapedComment}$/m", $output);
    }

    /**
     * @throws UniLexException
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

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid PHP code generated');
        /** @var TokenMatcherGenerator $generator */
        $generator->load();
    }

    /**
     * @throws UniLexException
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
            ->willReturn('');

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Failed to generate target class');
        /** @var TokenMatcherGenerator $generator */
        $generator->load();
    }

    /**
     * @param string $text
     * @param string $regExp
     * @param string $expectedValue
     * @throws UniLexException
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
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_MODE, $tokenSpec);
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
            "Single latin char with another char following" => ['ab', 'a', 'a'],
            "Single latin char with same char following" => ['aa', 'a', 'a'],
            "Zero or many latin char after single char" => ['baabc', 'ba*', 'baa'],
            "Number without leading zero" => ['103abc', '[1-9][0-9]*', '103'],
            'One or many symbols with Unicode property' => ['αβγabc', '\\p{Greek}+', 'αβγ'],
            'One or many symbols without Unicode property' => ['abcαβγ', '\\P{Greek}+', 'abc'],
            'Alternative before char' => ['abcc', '(a|b)+c', 'abc'],
            'Alternative of intersecting normal and negated classes' => ['bca', '([^ab]|[b])+', 'bc'],
            'One or more latin char' => ['aab', 'a+', 'aa'],
        ];
    }

    /**
     * @param string $text
     * @param string $firstRegExp
     * @param string $secondRegExp
     * @param int    $token
     * @param string $expectedValue
     * @throws UniLexException
     * @dataProvider providerTwoRegExpSpecsInSameMode
     */
    public function testLoad_TwoRegExpSpecsInSameMode_MatchesValidToken(
        string $text,
        string $firstRegExp,
        string $secondRegExp,
        int $token,
        string $expectedValue
    ): void {
        $matcherClass = $this->createTokenMatcherClassName();
        $spec = new TokenMatcherSpec($matcherClass, TokenMatcherTemplate::class);
        $firstCode = <<<SOURCE
\$context
    ->setNewToken(0)
    ->setTokenAttribute('text1', \$context->getSymbolString());
SOURCE;
        $secondCode = <<<SOURCE
\$context
    ->setNewToken(0)
    ->setTokenAttribute('text2', \$context->getSymbolString());
SOURCE;
        $firstTokenSpec = new TokenSpec($firstRegExp, $firstCode);
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_MODE, $firstTokenSpec);
        $secondTokenSpec = new TokenSpec($secondRegExp, $secondCode);
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_MODE, $secondTokenSpec);
        $generator = new TokenMatcherGenerator($spec);
        $buffer = CharBufferFactory::createFromString($text);
        $lexer = new TokenReader($buffer, $generator->load(), new \Remorhaz\UniLex\Lexer\TokenFactory(0xFF));
        $actualValue = $lexer
            ->read()
            ->getAttribute("text{$token}");
        self::assertSame($expectedValue, $actualValue);
    }

    public function providerTwoRegExpSpecsInSameMode(): array
    {
        return [
            'Different latin chars' => ['ab', 'a', 'b', 1, 'a'],
            'Alternatives with same prefix' => ['aab', 'a', 'ab', 1, 'a'],
            'Alternatives with different length' => ['..a', '\.', '\.{2}', 2, '..'],
            'Result matches both alternatives' => ['abc', 'ab', '[abd]+', 1, 'ab'],
            'Result matches second alternative' => ['abbc', 'ab', '[abd]+', 2, 'abb'],
        ];
    }

    /**
     * @param string $text
     * @param string $prefixRegExp
     * @param string $regExp
     * @param string $expectedValue
     * @dataProvider providerValidRegExpInputWithPrefix
     * @throws UniLexException
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
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_MODE, $tokenSpec);
        $prefixTokenSpec = new TokenSpec($prefixRegExp, $code);
        $spec->addTokenSpec(TokenMatcherInterface::DEFAULT_MODE, $prefixTokenSpec);
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
