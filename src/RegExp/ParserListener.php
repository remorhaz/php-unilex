<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LL1Parser\AbstractParserListener;

class ParserListener extends AbstractParserListener
{

    private $lexemeTypeLog = [];

    private $symbolLog = [];

    /**
     * @param Lexeme $lexeme
     */
    public function onLexeme(Lexeme $lexeme): void
    {
        switch ($lexeme->getType()) {
            default:
                $this->lexemeTypeLog[] = $lexeme->getType();
        }
    }

    public function onSymbol(int $symbolId, Lexeme $lexeme): void
    {
        switch ($symbolId) {
            default:
                $this->symbolLog[] = $symbolId;
        }
    }

    /**
     * @return array
     * @todo Debug method.
     */
    public function getLexemeTypeLog(): array
    {
        return $this->lexemeTypeLog;
    }

    /**
     * @return array
     * @todo Debug method.
     */
    public function getSymbolLog(): array
    {
        return $this->symbolLog;
    }
}
