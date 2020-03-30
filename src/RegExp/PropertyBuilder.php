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
        $ranges['Unknown'] = $unknownRanges;
        echo ". {$rangeCount} ranges\n";
        $source = null;

        echo "Building range sets: ";
        $rangeSetCount = 0;
        $rangeSets = [];
        foreach ($ranges as $prop => $rangeList) {
            $rangeSets[$prop] = new RangeSet(...$rangeList);
            echo ".";
            $rangeSetCount++;
        }
        echo " {$rangeSetCount} range sets\n";

        /*echo "Build Unknown property...";
        $fullRangeSet = new RangeSet(new Range(0x0, 0x10FFFF));
        $knownRangeSet = new RangeSet(...$knownRanges);
        $unknownRangeSet = $rangeSetCalc->xor($fullRangeSet, $knownRangeSet);
        $ranges['Unknown'] = $unknownRangeSet;*/

        return $this->dumpProps($index, $rangeSets);
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
