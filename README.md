# UniLex

[![Build Status](https://travis-ci.org/remorhaz/php-unilex.svg?branch=master)](https://travis-ci.org/remorhaz/php-unilex)
[![Maintainability](https://api.codeclimate.com/v1/badges/86f9f83bebfb4d44a210/maintainability)](https://codeclimate.com/github/remorhaz/php-unilex/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/86f9f83bebfb4d44a210/test_coverage)](https://codeclimate.com/github/remorhaz/php-unilex/test_coverage)

Unilex is lexical analyzer generator (similar to `lex` and `flex`) with Unicode support.
It's written in PHP and generates code in PHP.

## Requirements
* PHP 7.1+

```
[WIP] Work in progress
```

***
## License
UniLex library is licensed under MIT license.
## Installation
Installation is as simple as any other [composer](https://getcomposer.org/) library's one:
```
composer require remorhaz/php-unilex
```

## Usage
Library includes custom Phing tasks to build token matcher from specification:
```xml
<?xml version="1.0" encoding="UTF-8" ?>

<project name="unilex" basedir="." default="example-matcher">
    <taskdef classname="vendor.remorhaz.unilex.phing.BuildTokenMatcher" name="build-lexer" />
    <target name="example-matcher" description="Example Brainfuck token matcher">
        <build-lexer
            description="My example matcher matcher."
            sourceFile="${application.startdir}/path/to/spec/LexerSpec.php"
            destFile="${application.startdir}/path/to/target/TokenMatcher.php" />
    </target>
</project>
``` 

## Specification
Specification is a PHP file that is split in parts by DocBlock comments with special tags. There is a special variable `$context` that contains context object with `\Remorhaz\UniLex\Lexer\TokenMatcherContextInterface` interface. Current implementation also uses `int` variable `$char` that contains current symbol (TODO: should be moved into context object).
### @lexHeader
This block can contain `namespace` and `use` statements that will be used during matcher generation.
### @lexBeforeMatch
This block is executed before the beginning of matching procedure and can be used to initialize some additional variables.
### @lexOnTransition
This block is executed on each symbol matched by token's regular expression.
### @lexToken /regexp/
This block is executed on matching given regular expression from the input buffer. Most commonly it just setups new token in context object.
### @lexOnError
This block is executed if matcher fails to match any of token's regular expressions. By default it just returns `false`.
