<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp;

use Iterator;
use IteratorAggregate;
use Remorhaz\IntRangeSets\Range;
use SplFileObject;
use Throwable;

use function strlen;

final class UnicodeDataRangeIterator implements IteratorAggregate
{

    private $file;

    private $onProgress;

    private $code;

    private $name;

    private $prop;

    private $lastCode;

    private $lastProp;

    private $rangeStart;

    private $namedStarts = [];

    public function __construct(SplFileObject $file, callable $onProgress)
    {
        $this->file = $file;
        $this->onProgress = $onProgress;
    }

    public function getIterator(): Iterator
    {
        while (!$this->file->eof()) {
            $line = $this->fetchNextLine($this->file);
            if (!isset($line)) {
                continue;
            }
            $range = $this->fetchUnicodeDataRange($line);
            if (isset($range)) {
                yield $this->lastProp => $range;
            }

            $this->lastCode = $this->code;
            $this->lastProp = $this->prop;

            ($this->onProgress)(strlen($line));
        }
    }

    private function fetchNextLine(SplFileObject $file): ?string
    {
        $line = $file->fgets();
        if (false === $line) {
            throw new Exception\LineNotReadException($file->getFilename());
        }

        return '' == $line ? null : $line;
    }

    private function parseUnicodeDataLineLine(string $line): void
    {
        $splitLine = explode(';', $line);
        $codeHex = $splitLine[0] ?? null;
        $name = $splitLine[1] ?? null;
        $prop = $splitLine[2] ?? null;
        if (!isset($codeHex, $name, $prop)) {
            throw new Exception\InvalidLineException($line);
        }
        $this->code = hexdec($codeHex);
        $this->name = $name;
        $this->prop = $prop;
    }

    private function fetchUnicodeDataRange(string $line): ?Range
    {
        $this->parseUnicodeDataLineLine($line);

        [$firstName, $lastName] = $this->parseRangeBoundary($this->name);
        if (isset($firstName)) {
            $this->namedStarts[$firstName] = $this->code;
            $this->rangeStart = null;

            return null;
        }

        if (isset($lastName)) {
            if (
                !isset($this->namedStarts[$lastName]) ||
                isset($this->rangeStart) ||
                $this->lastCode !== $this->namedStarts[$lastName]
            ) {
                throw new Exception\InvalidLineException($line);
            }

            return $this->createRange($this->lastCode, $this->code);
        }

        if ($this->prop === $this->lastProp && $this->code - 1 === $this->lastCode) {
            return null;
        }

        $range = isset($this->rangeStart, $this->lastCode)
            ? $this->createRange($this->rangeStart, $this->lastCode)
            : null;

        $this->rangeStart = $this->code;

        return $range;
    }

    private function parseRangeBoundary(string $name): array
    {
        try {
            $isFirst = 1 === \Safe\preg_match('#^<(.+), First>$#', $name, $matches);
            if ($isFirst) {
                return [$matches[1] ?? null, null];
            }

            $isLast = 1 === \Safe\preg_match('#^<(.+), Last>$#', $name, $matches);

            return $isLast
                ? [null, $matches[1] ?? null]
                : [null, null];
        } catch (Throwable $e) {
            throw new Exception\CodePointNameNotParsedException($name, $e);
        }
    }

    private function createRange(int $start, ?int $finish): Range
    {
        try {
            return new Range($start, $finish);
        } catch (Throwable $e) {
            throw new Exception\RangeNotCreatedException($e);
        }
    }
}
