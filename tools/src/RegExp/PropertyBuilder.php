<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp;

use PhpParser\BuilderFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinterAbstract;
use ReflectionClass;
use Remorhaz\IntRangeSets\Range;
use Remorhaz\IntRangeSets\RangeInterface;
use Remorhaz\IntRangeSets\RangeSet;
use Remorhaz\IntRangeSets\RangeSetInterface;
use SplFileObject;
use Throwable;

use function array_diff;
use function array_keys;
use function array_merge;
use function count;
use function var_export;

final class PropertyBuilder
{

    private $rangeSets = [];

    private $scripts = [];

    private $phpBuilder;

    private $printer;

    /**
     * @var RangeInterface[][]
     */
    private $rangeBuffer = [];

    public function __construct(PrettyPrinterAbstract $printer)
    {
        $this->phpBuilder = new BuilderFactory();
        $this->printer = $printer;
    }

    public function parseUnicodeData(SplFileObject $file, callable $onProgress): void
    {
        foreach (new UnicodeDataRangeIterator($file, $onProgress) as $prop => $range) {
            $this->addRangeToBuffer($prop, $range);
        }
    }

    public function parseScripts(SplFileObject $file, callable $onProgress): void
    {
        $otherProperties = array_keys($this->rangeBuffer);
        $this->parseProperties($file, $onProgress);
        $this->scripts = array_diff(array_keys($this->rangeBuffer), $otherProperties);
    }

    public function parseProperties(SplFileObject $file, callable $onProgress): void
    {
        foreach (new PropertiesRangeIterator($file, $onProgress) as $prop => $range) {
            $this->addRangeToBuffer($prop, $range);
        }
    }

    private function addRangeToBuffer(string $prop, RangeInterface ...$ranges): void
    {
        $this->rangeBuffer[$prop] = array_merge($this->rangeBuffer[$prop] ?? [], $ranges);
    }

    public function getRangeBufferSize(): int
    {
        return count($this->rangeBuffer);
    }

    public function getFileCount(): int
    {
        return count($this->rangeSets) + 1;
    }

    public function fetchBufferedRangeSets(callable $onProgress): void
    {
        $count = 0;
        foreach ($this->rangeBuffer as $prop => $ranges) {
            $this->addRangeSet($prop, ...$ranges);
            $onProgress(++$count);
        }
        $this->rangeBuffer = [];
    }

    public function buildUnicodeDataDerivatives(callable $onProgress): void
    {
        $map = [
            'C' => ['Cc', 'Cf', 'Co', 'Cs'],
            'L' => ['Ll', 'Lm', 'Lo', 'Lt', 'Lu'],
            'L&' => ['Lu', 'Ll', 'Lt'],
            'M' => ['Mc', 'Me', 'Mn'],
            'N' => ['Nd', 'Nl', 'No'],
            'P' => ['Pc', 'Pd', 'Pe', 'Pf', 'Pi', 'Po', 'Ps'],
            'S' => ['Sc', 'Sk', 'Sm', 'So'],
            'Z' => ['Zl', 'Zp', 'Zs'],
        ];

        $notCnRanges = [];
        foreach ($map as $targetProp => $sourceProps) {
            foreach ($sourceProps as $prop) {
                $rangeSet = $this->getRangeSet($prop);
                $notCnRanges = array_merge($notCnRanges, $rangeSet->getRanges());
                $this->addRangeToBuffer($targetProp, ...$rangeSet->getRanges());
            }
        }

        try {
            $targetProp = 'Any';
            $anyRangeSet = RangeSet::createUnsafe(new Range(0x00, 0x10FFFF));
        } catch (Throwable $e) {
            throw new Exception\RangeSetNotBuiltException($targetProp, $e);
        }
        $onProgress();
        $this->addRangeToBuffer($targetProp, ...$anyRangeSet->getRanges());

        try {
            $targetProp = 'Cn';
            $notCnRangeSet = RangeSet::create(...$notCnRanges);
            $onProgress();
            $cnRanges = $notCnRangeSet
                ->createSymmetricDifference($anyRangeSet)
                ->getRanges();
        } catch (Throwable $e) {
            throw new Exception\RangeSetNotBuiltException($targetProp, $e);
        }
        $onProgress();
        $this->addRangeToBuffer($targetProp, ...$cnRanges);
    }

    public function buildScriptsDerivatives(callable $onProgress): void
    {
        $knownRanges = [];
        foreach ($this->scripts as $prop) {
            $knownRanges = array_merge($knownRanges, $this->getRangeSet($prop)->getRanges());
            $onProgress();
        }
        try {
            $targetProp = 'Unknown';
            $knownRangeSet = RangeSet::create(...$knownRanges);
            $onProgress();
            $unknownRanges = $knownRangeSet
                ->createSymmetricDifference($this->getRangeSet('Any'))
                ->getRanges();
        } catch (Throwable $e) {
            throw new Exception\RangeSetNotBuiltException($targetProp, $e);
        }
        $onProgress();
        $this->addRangeToBuffer($targetProp, ...$unknownRanges);
    }

    private function addRangeSet(string $prop, RangeInterface ...$ranges): void
    {
        if (isset($this->rangeSets[$prop])) {
            throw new Exception\RangeSetAlreadyExistsException($prop);
        }
        try {
            $this->rangeSets[$prop] = RangeSet::create(...$ranges);
        } catch (Throwable $e) {
            throw new Exception\RangeSetNotBuiltException($prop, $e);
        }
    }

    private function getRangeSet(string $prop): RangeSetInterface
    {
        if (isset($this->rangeSets[$prop])) {
            return $this->rangeSets[$prop];
        }

        throw new Exception\RangeSetNotFoundException($prop);
    }

    public function writeFiles(string $targetRootDir, callable $onProgress): void
    {
        $fileIndex = [];
        foreach ($this->rangeSets as $prop => $rangeSet) {
            $baseName = "/Properties/{$prop}.php";
            $fileName = $targetRootDir . $baseName;
            try {
                $code = $this->buildPropertyFile($rangeSet);
                \Safe\file_put_contents($fileName, $code);
            } catch (Throwable $e) {
                throw new Exception\FileNotWrittenException($fileName);
            }
            $fileIndex[$prop] = $baseName;
            $onProgress();
        }
        $fileName = $targetRootDir . "/PropertyIndex.php";
        try {
            $code = $this->buildIndexFile($fileIndex);
            \Safe\file_put_contents($fileName, $code);
        } catch (Throwable $e) {
            throw new Exception\FileNotWrittenException($fileName);
        }
        $onProgress();
    }

    private function buildIndexFile(array $index): string
    {
        $array = var_export($index, true);

        return "<?php\n\nreturn {$array};\n";
    }

    private function buildPropertyFile(RangeSetInterface $rangeSet): string
    {
        $rangeClass = new ReflectionClass(Range::class);
        $rangeSetClass = new ReflectionClass(RangeSet::class);

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
            $this->phpBuilder->staticCall($rangeSetClass->getShortName(), 'createUnsafe', $phpRanges)
        );
        $phpReturn->setDocComment(new Doc('/** phpcs:disable Generic.Files.LineLength.TooLong */'));
        $phpNodes[] = $phpReturn;

        return $this->printer->prettyPrintFile($phpNodes);
    }
}
