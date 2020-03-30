<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\RegExp\FSM\RangeSet;

final class PropertyLoader implements PropertyLoaderInterface
{

    private $index;

    private $cache = [];

    public static function create(): self
    {
        $index = require __DIR__ . '/PropertyIndex.php';

        return new self($index);
    }

    public function __construct(array $index)
    {
        $this->index = $index;
    }

    public function getPropertyRangeSet(string $name): RangeSet
    {
        if (!isset($this->cache[$name])) {
            $this->cache[$name] = $this->loadPropertyRangeSet($name);
        }

        return $this->cache[$name];
    }

    private function loadPropertyRangeSet(string $name): RangeSet
    {
        if (!isset($this->index[$name])) {
            throw new Exception\PropertyRangeSetNotFoundException($name);
        }

        $file = $this->index[$name];
        /** @noinspection PhpIncludeInspection */
        $rangeSet = require __DIR__ . $file;
        if ($rangeSet instanceof RangeSet) {
            return $rangeSet;
        }

        throw new Exception\UnicodePropertyRangeSetException($name);
    }
}
