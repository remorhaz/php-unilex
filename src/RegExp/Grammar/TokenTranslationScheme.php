<?php

namespace Remorhaz\UniLex\RegExp\Grammar;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Parser\ParsedToken;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;
use Throwable;

class TokenTranslationScheme
{

    private $symbol;

    private $token;

    /**
     * @param ParsedSymbol $symbol
     * @param ParsedToken $token
     * @throws Exception
     */
    public function applyActions(ParsedSymbol $symbol, ParsedToken $token): void
    {
        $this->setContext($symbol, $token);

        switch ($symbol->getSymbolId()) {
            case SymbolType::T_DIGIT_ZERO:
            case SymbolType::T_DIGIT_OCT:
                $this
                    ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                    ->copyTokenAttribute('s.oct_digit', 'digit')
                    ->copyTokenAttribute('s.dec_digit', 'digit')
                    ->copyTokenAttribute('s.hex_digit', 'digit');
                break;

            case SymbolType::T_DIGIT_DEC:
                $this
                    ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                    ->copyTokenAttribute('s.dec_digit', 'digit')
                    ->copyTokenAttribute('s.hex_digit', 'digit');
                break;

            case SymbolType::T_SMALL_C:
            case SymbolType::T_OTHER_HEX_LETTER:
                $this
                    ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                    ->copyTokenAttribute('s.hex_digit', 'digit');
                break;

            case SymbolType::T_COMMA:
            case SymbolType::T_HYPHEN:
            case SymbolType::T_CAPITAL_P:
            case SymbolType::T_RIGHT_SQUARE_BRACKET:
            case SymbolType::T_SMALL_O:
            case SymbolType::T_SMALL_P:
            case SymbolType::T_SMALL_U:
            case SymbolType::T_SMALL_X:
            case SymbolType::T_RIGHT_CURLY_BRACKET:
            case SymbolType::T_CTL_ASCII:
            case SymbolType::T_PRINTABLE_ASCII_OTHER:
            case SymbolType::T_NOT_ASCII:
            case SymbolType::T_DOLLAR:
            case SymbolType::T_LEFT_BRACKET:
            case SymbolType::T_RIGHT_BRACKET:
            case SymbolType::T_STAR:
            case SymbolType::T_PLUS:
            case SymbolType::T_QUESTION:
            case SymbolType::T_LEFT_SQUARE_BRACKET:
            case SymbolType::T_BACKSLASH:
            case SymbolType::T_CIRCUMFLEX:
            case SymbolType::T_LEFT_CURLY_BRACKET:
            case SymbolType::T_VERTICAL_LINE:
            case SymbolType::T_DOT:
            case SymbolType::T_OTHER_ASCII_LETTER:
                $this
                    ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR);
                break;
        }
    }

    /**
     * @param string $symbolAttr
     * @param string $tokenAttr
     * @return $this
     * @throws Exception
     */
    private function copyTokenAttribute(string $symbolAttr, string $tokenAttr)
    {
        try {
            $value = $this
                ->getToken()
                ->getToken()
                ->getAttribute($tokenAttr);
            $this
                ->getSymbol()
                ->setAttribute($symbolAttr, $value);
        } catch (Throwable $e) {
            $symbolText = "{$this->getSymbol()->getSymbolId()}.{$symbolAttr}";
            $tokenText = "{$this->getToken()->getToken()->getType()}.{$tokenAttr}";
            throw new Exception("Failed to synthesize attribute {$symbolText} from token {$tokenText}", 0, $e);
        }
        return $this;
    }


    private function setContext(ParsedSymbol $symbol, ParsedToken $token): void
    {
        $this->symbol = $symbol;
        $this->token = $token;
    }

    /**
     * @return ParsedSymbol
     * @throws Exception
     */
    private function getSymbol(): ParsedSymbol
    {
        if (!isset($this->symbol)) {
            throw new Exception("No symbol defined in token translation scheme");
        }
        return $this->symbol;
    }

    /**
     * @return ParsedToken
     * @throws Exception
     */
    private function getToken(): ParsedToken
    {
        if (!isset($this->token)) {
            throw new Exception("No token defined in token translation scheme");
        }
        return $this->token;
    }
}
