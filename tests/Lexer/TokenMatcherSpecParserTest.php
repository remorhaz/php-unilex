<?php

namespace Remorhaz\UniLex\Test\Lexer;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Lexer\TokenMatcherSpecParser;
use Remorhaz\UniLex\TokenMatcherTemplate;

/**
 * @covers \Remorhaz\UniLex\Lexer\TokenMatcherSpecParser
 */
class TokenMatcherSpecParserTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessageRegExp #^Failed to read lexer specification from file .+/NotExists\.php$#
     */
    public function testLoadFromFile_FileNotExists_ThrowsException(): void
    {
        TokenMatcherSpecParser::loadFromFile(__DIR__ . "/lex/NotExists.php");
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
     */
    public function testLoadFromFile_FileExists_MatcherSpecReturnsDefinedTargetClass(): void
    {
        $matcherSpec = TokenMatcherSpecParser::loadFromFile(__DIR__ . "/lex/Sample.php")->getMatcherSpec();
        $actualValue = $matcherSpec->getTargetClassName();
        self::assertSame("SampleTokenMatcher", $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid lexer specification: target class is not defined
     * @throws \ReflectionException
     */
    public function testGetMatcherSpec_TargetClassNameNotDefined_ThrowsException(): void
    {
        $source = <<<SOURCE
<?php        
/** @lexTemplateClass ClassName */
SOURCE;
        (new TokenMatcherSpecParser($source))->getMatcherSpec();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
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
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid lexer specification: duplicated @lexTargetClass tag
     * @throws \ReflectionException
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
        (new TokenMatcherSpecParser($source))->getMatcherSpec();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage nvalid lexer specification: duplicated @lexTemplateClass tag
     * @throws \ReflectionException
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
        (new TokenMatcherSpecParser($source))->getMatcherSpec();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
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
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
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
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
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
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
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
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Exception
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
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Exception
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
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid lexer specification: invalid used class expression
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
        (new TokenMatcherSpecParser($source))->getMatcherSpec();
    }

    /**
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Exception
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
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
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
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
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
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
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
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
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
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid lexer specification: @lexBeforeMatch tag conflicts with @lexHeader
     * @throws \ReflectionException
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
        (new TokenMatcherSpecParser($source))->getMatcherSpec();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid lexer specification: duplicated @lexHeader tag
     * @throws \ReflectionException
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
        (new TokenMatcherSpecParser($source))->getMatcherSpec();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
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
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
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
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid lexer specification: regular expression is not framed by "/"
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
        (new TokenMatcherSpecParser($source))->getMatcherSpec();
    }

    /**
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid lexer specification: duplicated @lexToken /abc/ tag
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
        (new TokenMatcherSpecParser($source))->getMatcherSpec();
    }

    /**
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Exception
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
        $actualValue = $matcherSpec->getTokenSpecList();
        self::assertCount(2, $actualValue);
    }

    /**
     * @throws \ReflectionException
     * @throws \Remorhaz\UniLex\Exception
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
        $tokenSpecList = $matcherSpec->getTokenSpecList();
        self::assertArrayHasKey('a', $tokenSpecList);
        self::assertSame("\$x = 0;", $tokenSpecList['a']->getCode());
        self::assertArrayHasKey('b', $tokenSpecList);
        self::assertSame("\$y = 1;", $tokenSpecList['b']->getCode());
    }
}
