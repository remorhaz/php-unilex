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
     */
    public function testGetMatcherSpec_SourceWithHeaderBlock_MatcherSpecGetHeaderReturnsBlockContents(): void
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
     */
    public function testGetMatcherSpec_SourceWithoutHeaderBlock_MatcherSpecGetHeaderReturnsEmptyString(): void
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
     */
    public function testGetMatcherSpec_SourceWithNamespaceInHeader_ReturnsBlockWithoutNamespace(): void
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
     */
    public function testGetMatcherSpec_SourceWithBeforeMatchBlock_MatcherSpecGetBeforeMatchReturnsBlockContents(): void
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
     */
    public function testGetMatcherSpec_SourceWithoutBeforeMatchBlock_MatcherSpecGetBeforeMatchReturnsEmptyString(): void
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
     */
    public function testGetMatcherSpec_SourceWithOnTransitionBlock_MatcherSpecGetOnTransitionMatchReturnsBlockContents(
    ): void {
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
     */
    public function testGetMatcherSpec_SourceWithoutOnTransitionBlock_MatcherSpecGetOnTransitionReturnsEmptyString(
    ): void {
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
}
