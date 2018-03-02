<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\EoiLexeme;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFreeGrammar;
use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeReaderInterface;

class Parser
{

    private $grammar;

    private $lookupTable;

    private $symbolStack = [];

    private $lexeme;

    private $lexemeReader;

    private $listener;

    public function __construct(
        ContextFreeGrammar $grammar,
        LexemeReaderInterface $lexemeReader,
        ParserListenerInterface $listener
    ) {
        $this->grammar = $grammar;
        $this->lexemeReader = $lexemeReader;
        $this->listener = $listener;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $this->prepareSymbolStack();
        while (!empty($this->symbolStack)) {
            $symbolId = $this->readSymbolId();
            $tokenId = $this->previewLexeme()->getType();
            if ($this->isTerminal($symbolId)) {
                if (!in_array($tokenId, $this->grammar->getTerminalMap()[$symbolId])) {
                    throw new Exception("Unexpected token {$tokenId} for symbol {$symbolId}");
                }
                $this->readLexeme();
                continue;
            }
            $symbolIdList = $this->getLookupTable()->getProduction($symbolId, $tokenId);
            $this->pushSymbols(...$symbolIdList);
        }
    }

    private function prepareSymbolStack(): void
    {
        $this->symbolStack = [];
        $this->pushSymbols($this->grammar->getStartSymbol(), $this->grammar->getEoiSymbol());
    }

    private function pushSymbols(int ...$symbolIdList): void
    {
        if (empty($symbolIdList)) {
            return;
        }
        array_push($this->symbolStack, ...array_reverse($symbolIdList));
    }

    /**
     * @return int
     * @throws Exception
     */
    private function readSymbolId(): int
    {
        if (empty($this->symbolStack)) {
            throw new Exception("Unexpected end of stack");
        }
        return array_pop($this->symbolStack);
    }

    private function previewLexeme(): Lexeme
    {
        if (!isset($this->lexeme)) {
            $this->lexeme = $this->lexemeReader->read();
        }
        return $this->lexeme;
    }

    private function isTerminal(int $symbolId): bool
    {
        $terminalMap = $this->grammar->getTerminalMap();
        return isset($terminalMap[$symbolId]);
    }

    private function readLexeme(): void
    {
        $lexeme = $this->previewLexeme();
        ($lexeme instanceof EoiLexeme)
            ? $this->listener->onEoi($lexeme)
            : $this->listener->onLexeme($lexeme);
        unset($this->lexeme);
    }

    /**
     * @return LookupTableInterface
     * @throws Exception
     */
    private function getLookupTable(): LookupTableInterface
    {
        if (!isset($this->lookupTable)) {
            $builder = new LookupTableBuilder($this->grammar);
            $this->lookupTable = $builder->getTable();
        }
        return $this->lookupTable;
    }
}
