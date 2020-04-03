<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp;

use Iterator;
use IteratorAggregate;
use Remorhaz\UniLex\RegExp\FSM\Range;
use SplFileObject;
use Throwable;

final class PropertiesRangeIterator implements IteratorAggregate
{

    private $file;

    private $onProgress;

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
            yield from $this->fetchPropertyRange($line);

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

    private function fetchPropertyRange(string $line): Iterator
    {
        $dataWithComment = explode('#', $line, 2);
        $data = trim($dataWithComment[0] ?? '');
        if ('' == $data) {
            return null;
        }
        $rangeWithProp = explode(';', $data);
        $unSplitRange = trim($rangeWithProp[0] ?? null);
        $prop = trim($rangeWithProp[1] ?? null);
        if (!isset($unSplitRange, $prop)) {
            throw new Exception\InvalidLineException($line);
        }
        $splitRange = explode('..', $unSplitRange);
        $start = hexdec($splitRange[0]);
        $finish = isset($splitRange[1])
            ? hexdec($splitRange[1])
            : $start;

        try {
            yield $prop => new Range($start, $finish);
        } catch (Throwable $e) {
            throw new Exception\RangeNotCreatedException($e);
        }
    }
}
