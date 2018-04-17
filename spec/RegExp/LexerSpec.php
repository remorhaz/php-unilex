<?php
/**
 * @lexHeader
 * @lexTargetClass TokenMatcher
 */

namespace Remorhaz\UniLex\RegExp\Grammar;

/**
 * @var \Remorhaz\UniLex\Lexer\TokenMatcherContextInterface $context
 * @var int $char
 *
 * @lexToken /[\u0000-\u001F]/
 */
$context
    ->setNewToken(TokenType::CTL_ASCII)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /[ -#%-'/:->@_~`]/ */
$context
    ->setNewToken(TokenType::PRINTABLE_ASCII_OTHER)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /\$/ */
$context
    ->setNewToken(TokenType::DOLLAR)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /\(/ */
$context
    ->setNewToken(TokenType::LEFT_BRACKET)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /\)/ */
$context
    ->setNewToken(TokenType::RIGHT_BRACKET)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /\u002A/ */
$context
    ->setNewToken(TokenType::STAR)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /\+/ */
$context
    ->setNewToken(TokenType::PLUS)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /,/ */
$context
    ->setNewToken(TokenType::COMMA)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /-/ */
$context
    ->setNewToken(TokenType::HYPHEN)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /\./ */
$context
    ->setNewToken(TokenType::DOT)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /0/ */
$context
    ->setNewToken(TokenType::DIGIT_ZERO)
    ->setTokenAttribute(TokenAttribute::CODE, $char)
    ->setTokenAttribute(TokenAttribute::DIGIT, chr($char));

/** @lexToken /[1-7]/ */
$context
    ->setNewToken(TokenType::DIGIT_OCT)
    ->setTokenAttribute(TokenAttribute::CODE, $char)
    ->setTokenAttribute(TokenAttribute::DIGIT, chr($char));

/** @lexToken /[8-9]/ */
$context
    ->setNewToken(TokenType::DIGIT_DEC)
    ->setTokenAttribute(TokenAttribute::CODE, $char)
    ->setTokenAttribute(TokenAttribute::DIGIT, chr($char));

/** @lexToken /\?/ */
$context
    ->setNewToken(TokenType::QUESTION)
    ->setTokenAttribute(TokenAttribute::CODE, $char);
/** @lexToken /[A-Fa-bd-f]/ */
$context
    ->setNewToken(TokenType::OTHER_HEX_LETTER)
    ->setTokenAttribute(TokenAttribute::CODE, $char)
    ->setTokenAttribute(TokenAttribute::DIGIT, chr($char));

/** @lexToken /[G-OQ-Zg-nq-tvwyz]/ */
$context
    ->setNewToken(TokenType::OTHER_ASCII_LETTER)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /P/ */
$context
    ->setNewToken(TokenType::CAPITAL_P)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /\[/ */
$context
    ->setNewToken(TokenType::LEFT_SQUARE_BRACKET)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /\\/ */
$context
    ->setNewToken(TokenType::BACKSLASH)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /]/ */
$context
    ->setNewToken(TokenType::RIGHT_SQUARE_BRACKET)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /\^/ */
$context
    ->setNewToken(TokenType::CIRCUMFLEX)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /c/ */
$context
    ->setNewToken(TokenType::SMALL_C)
    ->setTokenAttribute(TokenAttribute::CODE, $char)
    ->setTokenAttribute(TokenAttribute::DIGIT, chr($char));

/** @lexToken /o/ */
$context
    ->setNewToken(TokenType::SMALL_O)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /p/ */
$context
    ->setNewToken(TokenType::SMALL_P)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /u/ */
$context
    ->setNewToken(TokenType::SMALL_U)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /x/ */
$context
    ->setNewToken(TokenType::SMALL_X)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /\u007B/ */
$context
    ->setNewToken(TokenType::LEFT_CURLY_BRACKET)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /\|/ */
$context
    ->setNewToken(TokenType::VERTICAL_LINE)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /}/ */
$context
    ->setNewToken(TokenType::RIGHT_CURLY_BRACKET)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /\u007F/ */
$context
    ->setNewToken(TokenType::OTHER_ASCII)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/** @lexToken /[\u0080-\x{10FFFF}]/ */
$context
    ->setNewToken(TokenType::NOT_ASCII)
    ->setTokenAttribute(TokenAttribute::CODE, $char);

/**
 * @lexOnError
 */
if ($context->getBuffer()->isEnd()) {
    return false;
}
$char = $context->getBuffer()->getSymbol();
$context->getBuffer()->nextSymbol();
$context
    ->setNewToken(TokenType::INVALID)
    ->setTokenAttribute(TokenAttribute::CODE, $char);
return true;
