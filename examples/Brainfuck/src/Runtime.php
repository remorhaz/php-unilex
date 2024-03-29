<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Example\Brainfuck;

use Remorhaz\UniLex\Example\Brainfuck\Command\AbstractCommand;

class Runtime
{
    private const DEFAULT_MEMORY = 30_000;

    private array $dataList;

    private int $dataIndex = 0;

    private int $commandIndex = 0;

    private string $output = '';

    /**
     * @var AbstractCommand[]
     */
    private array $commandList = [];

    private int $nextCommandIndex = 0;

    /**
     * Runtime constructor.
     * @param int $memory
     * @throws Exception
     */
    public function __construct(
        private int $memory = self::DEFAULT_MEMORY,
    ) {
        if (0 >= $memory) {
            throw new Exception("Memory amount must be positive");
        }
        $this->dataList = array_fill(0, $this->memory, 0);
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function addCommand(AbstractCommand $command): void
    {
        $this->commandList[$this->nextCommandIndex] = $command;
        $command->setIndex($this->nextCommandIndex++);
    }

    /**
     * @throws Exception
     */
    public function shiftDataIndex(int $index): void
    {
        $this->setDataIndex($this->dataIndex + $index);
    }

    /**
     * @throws Exception
     */
    private function setDataIndex(int $index): void
    {
        $this->dataIndex = isset($this->dataList[$index])
            ? $index
            : throw new Exception("Failed to move data pointer to position $index");
    }

    public function shiftDataValue(int $value): void
    {
        $this->dataList[$this->dataIndex] += $value;
    }

    /**
     * @param int $index
     * @throws Exception
     */
    public function setCommandIndex(int $index): void
    {
        if (!isset($this->commandList[$index]) && $index != $this->nextCommandIndex) {
            throw new Exception("Failed to move command pointer to position {$index}");
        }
        $this->commandIndex = $index;
    }

    /**
     * @param int $index
     * @throws Exception
     */
    public function shiftCommandIndex(int $index): void
    {
        $this->setCommandIndex($this->commandIndex + $index);
    }

    public function isValueZero(): bool
    {
        return 0 == $this->dataList[$this->dataIndex];
    }

    public function outputData(): void
    {
        $this->output .= chr($this->dataList[$this->dataIndex]);
    }

    /**
     * @throws Exception
     */
    public function inputData(): void
    {
        throw new Exception("Data input is not implemented yet");
    }

    public function exec(): void
    {
        while ($this->commandIndex < $this->nextCommandIndex) {
            $this->commandList[$this->commandIndex]->exec();
        }
    }
}
