<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp;

use PhpParser\BuilderFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Return_;
use ReflectionClass;
use ReflectionException;
use Remorhaz\UniLex\Console\PrettyPrinter;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;
use RuntimeException;
use SplFileObject;

use function explode;
use function file_put_contents;
use function hexdec;
use function preg_match;
use function trim;

final class PropertyBuilder
{

    private const PROP_DIR = '/Properties';

    private $phpBuilder;

    private $printer;

    public function __construct()
    {
        $this->phpBuilder = new BuilderFactory();
        $this->printer = new PrettyPrinter();
    }

    /**
     * @param array $index
     * @return array
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function buildUnicodeData(array $index): array
    {
        $source = new SplFileObject(__DIR__ . '/../../data/UnicodeData.txt');
        $charCounter = 0;
        echo "Parsing: ";
        $ranges = [];
        $lastCode = null;
        $lastProp = null;
        $rangeStart = null;
        $namedStarts = [];
        while (!$source->eof()) {
            $line = $source->fgets();
            if (false === $line) {
                throw new RuntimeException("Error reading line from unicode data file");
            }
            if ('' == $line) {
                continue;
            }
            $splitLine = explode(';', $line);
            $codeHex = $splitLine[0] ?? null;
            $name = $splitLine[1] ?? null;
            $prop = $splitLine[2] ?? null;
            if (!isset($codeHex, $name, $prop)) {
                throw new RuntimeException("Invalid line format");
            }
            $code = hexdec($codeHex);
            $isFirst = 1 === preg_match('#^<(.+), First>$#', $name, $matches);
            $firstName = $matches[1] ?? null;
            $isLast = 1 === preg_match('#^<(.+), Last>$#', $name, $matches);
            $lastName = $matches[1] ?? null;
            $range = null;
            if ($isFirst) {
                $namedStarts[$firstName] = $code;
                unset($rangeStart);
            } elseif ($isLast) {
                if (!isset($namedStarts[$lastName]) || isset($rangeStart) || $lastCode !== $namedStarts[$lastName]) {
                    throw new RuntimeException("Invalid file format");
                }
                /** @var int $lastCode */
                $range = new Range($lastCode, $code);
            } elseif ($prop !== $lastProp) {
                /** @var int $rangeStart */
                if (isset($rangeStart, $lastCode)) {
                    $range = new Range($rangeStart, $lastCode);
                }

                $rangeStart = $code;
            }

            if (isset($range)) {
                if (!isset($ranges[$lastProp])) {
                    $ranges[$lastProp] = [];
                }
                $ranges[$lastProp][] = $range;
            }

            $lastCode = $code;
            $lastProp = $prop;

            if ($charCounter % 100 == 0) {
                echo ".";
            }
            $charCounter++;
        }
        $source = null;
        echo " {$charCounter} characters\n";

        return $this->dumpProps($index, $this->buildRangeSets($ranges));
    }

    /**
     * @param array $index
     * @return array
     * @throws UniLexException
     * @throws ReflectionException
     */
    public function buildScripts(array $index): array
    {
        /** @var RangeSet[] $ranges */
        $ranges = [];
        $source = new SplFileObject(__DIR__ . '/../../data/Scripts.txt');
        $lastKnownCode = null;
        $unknownRanges = [];

        echo "Parsing: ";
        $rangeCount = 0;

        while (!$source->eof()) {
            $line = $source->fgets();
            if (false === $line) {
                throw new RuntimeException("Error reading line from scripts file");
            }
            $dataWithComment = explode('#', $line, 2);
            $data = trim($dataWithComment[0] ?? '');
            if ('' == $data) {
                continue;
            }
            $rangeWithProp = explode(';', $data);
            $unsplittedRange = trim($rangeWithProp[0] ?? null);
            $prop = trim($rangeWithProp[1] ?? null);
            if (!isset($unsplittedRange, $prop)) {
                throw new RuntimeException("Invalid range or property");
            }
            $splittedRange = explode('..', $unsplittedRange);
            $start = hexdec($splittedRange[0]);
            $finish = isset($splittedRange[1])
                ? hexdec($splittedRange[1])
                : $start;
            if (!isset($lastKnownCode)) {
                if ($start > 0) {
                    $unknownRanges[] = new Range(0, $start - 1);
                }
            } elseif ($start - $lastKnownCode > 1) {
                $unknownRanges[] = new Range($lastKnownCode + 1, $start - 1);
            }
            $lastKnownCode = $finish;

            if (!isset($ranges[$prop])) {
                $ranges[$prop] = [];
            }
            $range = new Range($start, $finish);
            $ranges[$prop][] = $range;
            echo ".";
            $rangeCount++;
        }
        $source = null;
        $ranges['Unknown'] = $unknownRanges;
        echo ". {$rangeCount} ranges\n";

        return $this->dumpProps($index, $this->buildRangeSets($ranges));
    }

    /**
     * @param array $ranges
     * @return array
     * @throws UniLexException
     */
    private function buildRangeSets(array $ranges): array
    {
        echo "Building range sets: ";
        $rangeSetCount = 0;
        $rangeSets = [];
        foreach ($ranges as $prop => $rangeList) {
            $rangeSets[$prop] = new RangeSet(...$rangeList);
            echo ".";
            $rangeSetCount++;
        }
        echo " {$rangeSetCount} range sets\n";

        return $rangeSets;
    }

    /**
     * @param array $index
     * @param array $rangeSets
     * @return array
     * @throws ReflectionException
     */
    private function dumpProps(array $index, array $rangeSets): array
    {
        $rangeSetClass = new ReflectionClass(RangeSet::class);
        $rangeClass = new ReflectionClass(Range::class);
        foreach ($rangeSets as $prop => $rangeSet) {
            $targetFile = self::PROP_DIR . "/{$prop}.php";

            $phpNodes = [];
            $declare = new Declare_([new DeclareDeclare('strict_types', $this->phpBuilder->val(1))]);
            $declare->setDocComment(new Doc('/** @noinspection PhpUnhandledExceptionInspection */'));
            $phpNodes[] = $declare;
            $phpNodes[] = $this->phpBuilder->namespace(__NAMESPACE__ . '\\Properties')->getNode();
            $phpNodes[] = $this->phpBuilder->use($rangeClass->getName())->getNode();
            $phpNodes[] = $this->phpBuilder->use($rangeSetClass->getName())->getNode();
            $phpRanges = [];

            foreach ($rangeSet->getRanges() as $range) {
                $rangeStart = $range->getStart();
                $rangeFinish = $range->getFinish();
                $phpRangeStart = $this->phpBuilder->val($rangeStart);
                $phpRangeStart->setAttribute('kind', LNumber::KIND_HEX);
                $phpRangeArgs = [$phpRangeStart];
                if ($rangeStart != $rangeFinish) {
                    $phpRangeFinish = $this->phpBuilder->val($rangeFinish);
                    $phpRangeFinish->setAttribute('kind', LNumber::KIND_HEX);
                    $phpRangeArgs[] = $phpRangeFinish;
                }
                $phpRanges[] = $this->phpBuilder->new($rangeClass->getShortName(), $phpRangeArgs);
            }
            $phpReturn = new Return_(
                $this->phpBuilder->staticCall($rangeSetClass->getShortName(), 'loadUnsafe', $phpRanges)
            );
            $phpReturn->setDocComment(new Doc('/** phpcs:disable Generic.Files.LineLength.TooLong */'));
            $phpNodes[] = $phpReturn;
            file_put_contents(__DIR__ . $targetFile, $this->printer->prettyPrintFile($phpNodes));
            $index[$prop] = $targetFile;
        }

        return $index;
    }

    public function dumpIndex(array $index): void
    {
        $indexCode = "<?php\n\nreturn " . var_export($index, true) . ";\n";
        file_put_contents(__DIR__ . '/PropertyIndex.php', $indexCode);
    }
}
