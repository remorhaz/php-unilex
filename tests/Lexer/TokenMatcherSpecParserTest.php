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
        TokenMatcherSpecParser::loadFromFile(__DIR__ . "/NotExists.php");
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
/**
 * @lexTemplateClass ClassName
 */
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
/**
 * @lexTargetClass ClassName
 */
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
}
