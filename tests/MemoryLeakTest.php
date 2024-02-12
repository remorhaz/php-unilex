<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\IO\CharBuffer;
use Remorhaz\UniLex\Lexer\TokenMatcherByType;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\Parser\LL1\AbstractParserListener;
use Remorhaz\UniLex\Parser\LL1\Lookup\TableBuilder;
use Remorhaz\UniLex\Parser\LL1\Parser;
use Remorhaz\UniLex\Parser\LL1\UnexpectedTokenException;
use Safe;
use Safe\Exceptions\FilesystemException;

use function array_fill;
use function array_unique;
use function memory_get_usage;
use function var_export;

#[CoversNothing]
class MemoryLeakTest extends TestCase
{
    /**
     * @throws FilesystemException
     * @throws UniLexException
     * @throws UnexpectedTokenException
     */
    public function testMemoryLeak(): void
    {
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $listener = $this->createStub(AbstractParserListener::class);
        $tableBuilder = new TableBuilder($grammar);
        $lookupTable = __DIR__ . '/../build/SimpleExprLookup.php';
        $lookupTableDump = var_export($tableBuilder->getTable()->exportMap(), true);
        $content = "<?php return $lookupTableDump;\n";
        Safe\file_put_contents($lookupTable, $content);

        $attemptCount = 1000;
        $memory = array_fill(0, $attemptCount, 0);
        for ($i = 0; $i < $attemptCount; $i++) {
            $buffer = new CharBuffer(TokenType::ID, TokenType::PLUS, TokenType::ID);
            $reader = new TokenReader($buffer, new TokenMatcherByType(), new TokenFactory($grammar));
            $parser = new Parser($grammar, $reader, $listener);
            $parser->loadLookupTable($lookupTable);
            $parser->run();
            $memory[$i] = memory_get_usage(true);
        }
        self::assertCount(1, array_unique($memory));
    }
}
