<?php

/**
 * Brainfuck token matcher.
 *
 * Auto-generated file, please don't edit manually.
 * Generated by UniLex.
 */

declare(strict_types=1);

namespace Remorhaz\UniLex\Example\Brainfuck\Grammar;

use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\Lexer\TokenFactoryInterface;
use Remorhaz\UniLex\Lexer\TokenMatcherTemplate;

class TokenMatcher extends TokenMatcherTemplate
{

    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
    {
        $context = $this->createContext($buffer, $tokenFactory);
        $context->setRegExps(
            'default',
            '>',
            '<',
            '\\+',
            '-',
            '\\.',
            ',',
            '\\[',
            ']'
        );
        goto state1;

        state1:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x3E == $char) {
            $context->getBuffer()->nextSymbol();
            $context->allowRegExps('>');
            goto state2;
        }
        if (0x3C == $char) {
            $context->getBuffer()->nextSymbol();
            $context->allowRegExps('<');
            goto state2;
        }
        if (0x2B == $char) {
            $context->getBuffer()->nextSymbol();
            $context->allowRegExps('\\+');
            goto state2;
        }
        if (0x2D == $char) {
            $context->getBuffer()->nextSymbol();
            $context->allowRegExps('-');
            goto state2;
        }
        if (0x2E == $char) {
            $context->getBuffer()->nextSymbol();
            $context->allowRegExps('\\.');
            goto state2;
        }
        if (0x2C == $char) {
            $context->getBuffer()->nextSymbol();
            $context->allowRegExps(',');
            goto state2;
        }
        if (0x5B == $char) {
            $context->getBuffer()->nextSymbol();
            $context->allowRegExps('\\[');
            goto state2;
        }
        if (0x5D == $char) {
            $context->getBuffer()->nextSymbol();
            $context->allowRegExps(']');
            goto state2;
        }
        goto error;

        state2:
        switch ($context->getRegExp()) {
            case '>':
                $context->setNewToken(TokenType::NEXT);

                return true;

            case '<':
                $context->setNewToken(TokenType::PREV);

                return true;

            case '\\+':
                $context->setNewToken(TokenType::INC);

                return true;

            case '-':
                $context->setNewToken(TokenType::DEC);

                return true;

            case '\\.':
                $context->setNewToken(TokenType::OUTPUT);

                return true;

            case ',':
                $context->setNewToken(TokenType::INPUT);

                return true;

            case '\\[':
                $context->setNewToken(TokenType::LOOP);

                return true;

            case ']':
                $context->setNewToken(TokenType::END_LOOP);

                return true;

            default:
                goto error;
        }

        error:
        return false;
    }
}
