# Example: Brainfuck interpreter
[Brainfuck](https://en.wikipedia.org/wiki/Brainfuck) is an esoteric programming language with extremely simple syntax.
## Grammar 
Brainfuck can be expressed using the following context-free LL(1) grammar:
```
EXPRESSION → COMMAND EXPRESSION | LOOP EXPRESSION | ε
COMMAND → ">" | "<" | "+" | "-" | "." | ","
LOOP → "[" EXPRESSION "]"
```
This grammar is configured in `Grammar/Config.php`. Configuration includes:
* Bijection between tokens and terminals.
* Production list.
* Some additional data: "root", "start" and "end of input" symbols.
## Lexer
Lexer converts raw input stream into stream of tokens. Interpreter utilizes `Remorhaz\UniLex\TokenReader` class for that purpose. To recognize Brainfuck tokens we need to build a matcher. Matcher configuration is defined in `Grammar/TokenMatcherConfig.php`, and matcher itself can be generated with Phing using this command:
```
vendor/bin/phing example-brainfuck-matcher
```
Result of generation procedure is `Remorhaz\UniLex\Example\Brainfuck\Grammar\TokenMatcher` class which is a grammar-dependent part of lexer.
## Parser
LL(1) parser needs a lookup table. This table can be generated with Phing using this command:
```
vendor/bin/phing example-brainfuck-table
```
Result of generation procedure is `generated/Brainfuck/Grammar/LookupTable.php` file that returns table as an array on inclusion.
## SDT scheme
Interpreter uses syntax-driven translation scheme to put commands in a buffer. Most of commands can be constructed just after arriving of corresponding token in input stream (`TranslationSchemeInterface::applyTokenActions()` method), and only the "end of loop" non-terminal requires additional workaround to set up command index shifts after parsing LOOP production (`TranslationSchemeInterface::applyProductionActions()` method).
## Code example
```php
    $code =
        "++++++++++[>+++++++>++++++++++>+++>+<<<<-]>++" .
        ".>+.+++++++..+++.>++.<<+++++++++++++++.>.+++." .
        "------.--------.>+.>.";
    $interpreter = new Interpreter;
    $interpreter->exec($code);
    echo $interpreter->getOutput(); // Prints "Hello World!\n"

```