<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\RegExp\FSM\RangeSet;

use function error_clear_last;
use function error_get_last;
use function is_string;

final class PropertyLoader implements PropertyLoaderInterface
{

    private $path;

    private $index;

    private $cache = [];

    public static function create(): self
    {
        $index = require __DIR__ . '/PropertyIndex.php';

        return new self(__DIR__, $index);
    }

    public function __construct(string $path, array $index)
    {
        $this->path = $path;
        $this->index = $index;
    }

    public function getRangeSet(string $propertyName): RangeSet
    {
        if (!isset($this->cache[$propertyName])) {
            $this->cache[$propertyName] = $this->loadRangeSet($propertyName);
        }

        return $this->cache[$propertyName];
    }

    private function loadRangeSet(string $propertyName): RangeSet
    {
        if (!isset($this->index[$propertyName])) {
            throw new Exception\PropertyRangeSetNotFoundException($propertyName);
        }

        $file = $this->index[$propertyName];
        if (!is_string($file)) {
            throw new Exception\InvalidPropertyConfigException($propertyName, $file);
        }
        $fileName = $this->path . $file;
        error_clear_last();
        /** @noinspection PhpIncludeInspection */
        $rangeSet = @include $fileName;
        if (false === $rangeSet) {
            $lastError = error_get_last();
            if (isset($lastError)) {
                throw new Exception\PropertyFileNotLoadedException(
                    $propertyName,
                    $fileName,
                    $lastError['message'] ?? null
                );
            }
        }
        if ($rangeSet instanceof RangeSet) {
            return $rangeSet;
        }

        throw new Exception\InvalidPropertyRangeSetException($propertyName, $fileName, $rangeSet);
    }
}
