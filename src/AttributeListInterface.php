<?php

declare(strict_types=1);

namespace Remorhaz\UniLex;

interface AttributeListInterface
{
    public function getAttribute(string $name);

    public function setAttribute(string $name, $value);

    public function attributeExists(string $name): bool;
}
