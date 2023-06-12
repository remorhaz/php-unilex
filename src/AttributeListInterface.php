<?php

declare(strict_types=1);

namespace Remorhaz\UniLex;

interface AttributeListInterface
{
    public function getAttribute(string $name): mixed;

    public function setAttribute(string $name, mixed $value): static;

    public function attributeExists(string $name): bool;
}
