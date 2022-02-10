<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Example\Brainfuck\Grammar;

use Remorhaz\UniLex\Example\Brainfuck\Exception;
use Remorhaz\UniLex\Example\Brainfuck\Command\LoopCommand;
use Remorhaz\UniLex\Example\Brainfuck\Command\OutputCommand;
use Remorhaz\UniLex\Example\Brainfuck\Runtime;
use Remorhaz\UniLex\Example\Brainfuck\Command\SetCommandIndexCommand;
use Remorhaz\UniLex\Example\Brainfuck\Command\ShiftDataIndexCommand;
use Remorhaz\UniLex\Example\Brainfuck\Command\ShiftDataValueCommand;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;
use Remorhaz\UniLex\Lexer\Token;
use Throwable;

class TranslationScheme implements TranslationSchemeInterface
{
    private $runtime;

    public function __construct(Runtime $runtime)
    {
        $this->runtime = $runtime;
    }

    /**
     * @param Production $production
     * @throws Exception
     */
    public function applyProductionActions(Production $production): void
    {
        $hash = "{$production->getHeader()->getSymbolId()}.{$production->getIndex()}";
        switch ($hash) {
            case SymbolType::NT_LOOP . ".0":
                try {
                    $loopCommand = $production
                        ->getSymbol(0)
                        ->getAttribute('loop_command');
                } catch (Throwable $e) {
                    throw new Exception("Loop start command not found", 0, $e);
                }
                if (!$loopCommand instanceof LoopCommand) {
                    throw new Exception("Invalid loop start command");
                }
                $command = new SetCommandIndexCommand($this->runtime, $loopCommand->getIndex());
                $this->runtime->addCommand($command);
                $loopCommand->setEndLoopIndex($command->getIndex());
                break;
        }
    }

    public function applySymbolActions(Production $production, int $symbolIndex): void
    {
    }

    /**
     * @param Symbol $symbol
     * @param Token $token
     * @throws Exception
     */
    public function applyTokenActions(Symbol $symbol, Token $token): void
    {
        switch ($token->getType()) {
            case TokenType::NEXT:
                $command = new ShiftDataIndexCommand($this->runtime, 1);
                $this->runtime->addCommand($command);
                break;

            case TokenType::PREV:
                $command = new ShiftDataIndexCommand($this->runtime, -1);
                $this->runtime->addCommand($command);
                break;

            case TokenType::INC:
                $command = new ShiftDataValueCommand($this->runtime, 1);
                $this->runtime->addCommand($command);
                break;

            case TokenType::DEC:
                $command = new ShiftDataValueCommand($this->runtime, -1);
                $this->runtime->addCommand($command);
                break;

            case TokenType::OUTPUT:
                $command = new OutputCommand($this->runtime);
                $this->runtime->addCommand($command);
                break;

            case TokenType::LOOP:
                $command = new LoopCommand($this->runtime);
                $this->runtime->addCommand($command);
                try {
                    $symbol->setAttribute('loop_command', $command);
                } catch (Throwable $e) {
                    throw new Exception("Failed to setup loop calculation");
                }
                break;
        }
    }
}
