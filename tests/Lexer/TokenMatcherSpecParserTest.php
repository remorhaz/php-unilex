<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Lexer;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Lexer\TokenMatcherInterface;
use Remorhaz\UniLex\Lexer\TokenMatcherSpecParser;
use Remorhaz\UniLex\Lexer\TokenMatcherTemplate;

/**
 * @covers \Remorhaz\UniLex\Lexer\TokenMatcherSpecParser
 */
class TokenMatcherSpecParserTest extends TestCase
{
    /**
     * @throws UniLexException
     */
    public function testLoadFromFile_FileNotExists_ThrowsException(): void
    {
        $this->expectException(UniLexException::class);
        $this->expectExceptionMessageMatches(
            '#^Failed to read lexer specification from file .+/NotExists\.php$#'
        );
        TokenMatcherSpecParser::loadFromFile(__DIR__ . "/lex/NotExists.php");
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testLoadFromFile_FileExists_MatcherSpecReturnsDefinedTargetClass(): void
    {
        $matcherSpec = TokenMatcherSpecParser::loadFromFile(__DIR__ . "/lex/Sample.php")->getMatcherSpec();
        $actualValue = $matcherSpec->getTargetClassName();
        self::assertSame("SampleTokenMatcher", $actualValue);
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_TargetClassNameNotDefined_ThrowsException(): void
    {
        $source = <<<SOURCE
<?php        
/** @lexTemplateClass ClassName */
SOURCE;
        $matcherSpec = new TokenMatcherSpecParser($source);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid lexer specification: target class is not defined');
        $matcherSpec->getMatcherSpec();
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_TemplateClassNameNotDefined_MatcherSpecUsesDefaultTemplateClass(): void
    {
        $source = <<<SOURCE
<?php        
/** @lexTargetClass ClassName */
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getTemplateClass()->getName();
        self::assertSame(TokenMatcherTemplate::class, $actualValue);
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_TargetClassDefinedTwice_ThrowsException(): void
    {
        $source = <<<SOURCE
<?php        
/**
 * @lexTargetClass ClassName
 * @lexTargetClass ClassName
 */
SOURCE;
        $matcherSpec = new TokenMatcherSpecParser($source);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid lexer specification: duplicated @lexTargetClass tag');
        $matcherSpec->getMatcherSpec();
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_TemplateClassDefinedTwice_ThrowsException(): void
    {
        $source = <<<SOURCE
<?php        
/**
 * @lexTemplateClass ClassName
 * @lexTemplateClass ClassName
 */
SOURCE;
        $matcherSpec = new TokenMatcherSpecParser($source);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid lexer specification: duplicated @lexTemplateClass tag');
        $matcherSpec->getMatcherSpec();
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_SourceHeaderBlock_MatcherSpecGetHeaderReturnsBlockContents(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexHeader
 * @lexTargetClass ClassName
 */
\$x = 0;
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getHeader();
        self::assertSame("\$x = 0;", $actualValue);
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_SourceHeaderBlock_MatcherSpecGetHeaderReturnsEmptyString(): void
    {
        $source = <<<SOURCE
<?php
/** @lexTargetClass ClassName */
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getHeader();
        self::assertSame("", $actualValue);
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_SourceNamespaceInHeader_ReturnsBlockWithoutNamespace(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexHeader
 */
namespace Remorhaz\UniLex\Tests\Lexer;

\$x = 0;
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getHeader();
        self::assertSame("\$x = 0;", $actualValue);
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_SourceNamespaceInHeader_MatcherSpecTargetClassHasSameNamespace(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexHeader
 */
namespace Namespace;
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getTargetClassName();
        self::assertSame("Namespace\ClassName", $actualValue);
    }

    /**
     * @throws ReflectionException
     * @throws UniLexException
     */
    public function testGetMatcherSpec_SourceUseInHeader_MatcherSpecHasMatchingUseInList(): void
    {
        $class = __CLASS__;
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexHeader
 */
use {$class};
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $useList = $matcherSpec->getUsedClassList();
        self::assertContains($class, $useList);
    }

    /**
     * @throws ReflectionException
     * @throws UniLexException
     */
    public function testGetMatcherSpec_SourceUseAsAliasInHeader_MatcherSpecHasMatchingUseInList(): void
    {
        $class = __CLASS__;
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexHeader
 */
use {$class} as FooBar;
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $useList = $matcherSpec->getUsedClassList();
        self::assertArrayHasKey('FooBar', $useList);
        self::assertSame($class, $useList['FooBar']);
    }

    /**
     * @throws ReflectionException
     * @throws UniLexException
     */
    public function testGetMatcherSpec_SourceInvalidUseInHeader_ThrowsException(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexHeader
 */
use Namespace\Class aas FooBar;
SOURCE;
        $matcherSpec = new TokenMatcherSpecParser($source);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid lexer specification: invalid used class expression');
        $matcherSpec->getMatcherSpec();
    }

    /**
     * @throws ReflectionException
     * @throws UniLexException
     */
    public function testGetMatcherSpec_SourceUseInHeader_ReturnsBlockWithoutUse(): void
    {
        $class = __CLASS__;
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexHeader
 */
use {$class};
\$x = 0;
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getHeader();
        self::assertSame("\$x = 0;", $actualValue);
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_SourceBeforeMatchBlock_MatcherSpecGetBeforeMatchReturnsBlockContents(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexBeforeMatch
 * @lexTargetClass ClassName
 */
\$x = 0;
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getBeforeMatch();
        self::assertSame("\$x = 0;", $actualValue);
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_SourceNoBeforeMatchBlock_MatcherSpecGetBeforeMatchReturnsEmptyString(): void
    {
        $source = <<<SOURCE
<?php
/** @lexTargetClass ClassName */
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getBeforeMatch();
        self::assertSame("", $actualValue);
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_SourceOnTransitionBlock_MatcherSpecGetOnTransitionReturnsBlockContents(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexOnTransition
 * @lexTargetClass ClassName
 */
\$x = 0;
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getOnTransition();
        self::assertSame("\$x = 0;", $actualValue);
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_SourceNoOnTransitionBlock_MatcherSpecGetOnTransitionReturnsEmptyString(): void
    {
        $source = <<<SOURCE
<?php
/** @lexTargetClass ClassName */
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getOnTransition();
        self::assertSame("", $actualValue);
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_SourceWithBlockConflict_ThrowsException(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexHeader
 * @lexBeforeMatch
 * @lexTargetClass ClassName
 */
SOURCE;
        $matcherSpec = new TokenMatcherSpecParser($source);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid lexer specification: @lexBeforeMatch tag conflicts with @lexHeader');
        $matcherSpec->getMatcherSpec();
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_SourceDuplicatedBlock_ThrowsException(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexHeader
 * @lexTargetClass ClassName
 */
/** @lexHeader */
SOURCE;
        $matcherSpec = new TokenMatcherSpecParser($source);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid lexer specification: duplicated @lexHeader tag');
        $matcherSpec->getMatcherSpec();
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_SourceWithoutOnErrorBlock_MatcherSpecGetOnErrorReturnsReturnFalseCode(): void
    {
        $source = <<<SOURCE
<?php
/** @lexTargetClass ClassName */
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getOnError();
        self::assertSame("return false;", $actualValue);
    }

    /**
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function testGetMatcherSpec_SourceWithOnErrorBlock_MatcherSpecGetOnErrorReturnsMatchingBlock(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexOnError
 */
return true; 
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getOnError();
        self::assertSame("return true;", $actualValue);
    }

    /**
     * @throws ReflectionException
     * @throws UniLexException
     */
    public function testGetMatcherSpec_SourceWithInvalidRegExp_ThrowsException(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexToken abc
 */
SOURCE;
        $matcherSpec = new TokenMatcherSpecParser($source);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid lexer specification: regular expression is not framed by "/"');
        $matcherSpec->getMatcherSpec();
    }

    /**
     * @throws ReflectionException
     * @throws UniLexException
     */
    public function testGetMatcherSpec_DuplicatedRegExp_ThrowsException(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexToken /abc/
 */
\$x = 0;
/** @lexToken /abc/ */
\$y = 1;
SOURCE;
        $matcherSpec = new TokenMatcherSpecParser($source);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid lexer specification: duplicated @lexToken /abc/ tag');
        $matcherSpec->getMatcherSpec();
    }

    /**
     * @throws ReflectionException
     * @throws UniLexException
     */
    public function testGetMatcherSpec_TwoValidTokens_MatcherSpecGetTokenSpecListReturnsTwoElements(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexToken /a/
 */
\$x = 0;
/** @lexToken /b/ */
\$y = 1;
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $actualValue = $matcherSpec->getTokenSpecList(TokenMatcherInterface::DEFAULT_MODE);
        self::assertCount(2, $actualValue);
    }

    /**
     * @throws ReflectionException
     * @throws UniLexException
     */
    public function testGetMatcherSpec_TwoValidTokens_MatcherSpecGetTokenSpecListContainsBothMatchingTokens(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexToken /a/
 */
\$x = 0;
/** @lexToken /b/ */
\$y = 1;
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $tokenSpecList = $matcherSpec->getTokenSpecList(TokenMatcherInterface::DEFAULT_MODE);
        self::assertArrayHasKey('a', $tokenSpecList);
        self::assertSame("\$x = 0;", $tokenSpecList['a']->getCode());
        self::assertArrayHasKey('b', $tokenSpecList);
        self::assertSame("\$y = 1;", $tokenSpecList['b']->getCode());
    }

    /**
     * @throws ReflectionException
     * @throws UniLexException
     */
    public function testGetMatcherSpec_TwoTokensNotSameContext_MatcherSpecGetTokenSpecListsContainMatchingTokens(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexMode custom
 * @lexToken /a/
 */
\$x = 0;
/** @lexToken /a/ */
\$y = 1;
SOURCE;
        $matcherSpec = (new TokenMatcherSpecParser($source))->getMatcherSpec();
        $defaultTokenSpecList = $matcherSpec->getTokenSpecList(TokenMatcherInterface::DEFAULT_MODE);
        self::assertArrayHasKey('a', $defaultTokenSpecList);
        self::assertSame("\$y = 1;", $defaultTokenSpecList['a']->getCode());
        $customTokenSpecList = $matcherSpec->getTokenSpecList('custom');
        self::assertArrayHasKey('a', $customTokenSpecList);
        self::assertSame("\$x = 0;", $customTokenSpecList['a']->getCode());
    }

    /**
     * @throws ReflectionException
     * @throws UniLexException
     */
    public function testGetMatcherSpec_InvalidContext_ThrowsException(): void
    {
        $source = <<<SOURCE
<?php
/**
 * @lexTargetClass ClassName
 * @lexMode #invalid
 * @lexToken /a/
 */
\$x = 0;
SOURCE;
        $matcherSpec = new TokenMatcherSpecParser($source);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid lexer specification: invalid context name #invalid');
        $matcherSpec->getMatcherSpec();
    }
}
