<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\AttributeListInterface;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Parser\AttributeListShortcut;

class Token implements AttributeListInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $attributeList = [];

    public function __construct(
        private readonly int $type,
        private readonly bool $isEoi,
    ) {
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function isEoi(): bool
    {
        return $this->isEoi;
    }

    /**
     * @throws Exception
     */
    public function setAttribute(string $name, mixed $value): static
    {
        $this->attributeList[$name] = isset($this->attributeList[$name])
            ? throw new Exception("Token attribute '{$name}' is already set")
            : $value;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function getAttribute(string $name): mixed
    {
        return $this->attributeList[$name]
            ?? throw new Exception("Token attribute '$name' is not defined");
    }

    public function attributeExists(string $name): bool
    {
        return isset($this->attributeList[$name]);
    }

    public function getShortcut(): AttributeListShortcut
    {
        return new AttributeListShortcut($this);
    }
}
