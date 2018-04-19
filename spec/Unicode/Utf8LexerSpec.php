<?php
/**
 * @lexHeader
 * @lexTargetClass Utf8TokenMatcher
 */

namespace Remorhaz\UniLex\Unicode\Grammar;

/**
 * @var \Remorhaz\UniLex\Lexer\TokenMatcherContextInterface $context
 * @var int $char
 *
 * @lexOnTransition
 */
$context->storeCurrentSymbol();

/**
 * 1-byte symbol
 *
 * @lexToken /[\x00-\x7F]/
 */
$context
    ->setNewToken(TokenType::SYMBOL)
    ->setTokenAttribute(TokenAttribute::UNICODE_CHAR, $char);

/**
 * 2-byte symbol
 *
 * @lexToken /[\xC0-\xDF][\x80-\xBF]/
 */
$charList = $context->getStoredSymbolList();
$symbol = ($charList[0] & 0x1F) << 6;
$symbol |= ($charList[1] & 0x3F);
$context
    ->setNewToken(TokenType::SYMBOL)
    ->setTokenAttribute(TokenAttribute::UNICODE_CHAR, $symbol);

/**
 * 3-byte symbol
 *
 * @lexToken /[\xE0-\xEF][\x80-\xBF]{2}/
 */
$charList = $context->getStoredSymbolList();
$symbol = ($charList[0] & 0x0F) << 12;
$symbol |= ($charList[1] & 0x3F) << 6;
$symbol |= ($charList[2] & 0x3F);
$context
    ->setNewToken(TokenType::SYMBOL)
    ->setTokenAttribute(TokenAttribute::UNICODE_CHAR, $symbol);

/**
 * 4-byte symbol
 *
 * @lexToken /[\xF0-\xF7][\x80-\xBF]{3}/
 */
$charList = $context->getStoredSymbolList();
$symbol = ($charList[0] & 0x07) << 18;
$symbol |= ($charList[1] & 0x3F) << 12;
$symbol |= ($charList[2] & 0x3F) << 6;
$symbol |= ($charList[3] & 0x3F);
$context
    ->setNewToken(TokenType::SYMBOL)
    ->setTokenAttribute(TokenAttribute::UNICODE_CHAR, $symbol);

/**
 * 5-byte symbol
 *
 * @lexToken /[\xF8-\xFB][\x80-\xBF]{4}/
 */
$charList = $context->getStoredSymbolList();
$symbol = ($charList[0] & 0x03) << 24;
$symbol |= ($charList[1] & 0x3F) << 18;
$symbol |= ($charList[2] & 0x3F) << 12;
$symbol |= ($charList[3] & 0x3F) << 6;
$symbol |= ($charList[4] & 0x3F);
$context
    ->setNewToken(TokenType::SYMBOL)
    ->setTokenAttribute(TokenAttribute::UNICODE_CHAR, $symbol);

/**
 * 6-byte symbol
 *
 * @lexToken /[\xFC-\xFD][\x80-\xBF]{5}/
 */
$charList = $context->getStoredSymbolList();
$symbol = ($charList[0] & 0x01) << 30;
$symbol |= ($charList[1] & 0x03) << 24;
$symbol |= ($charList[2] & 0x3F) << 18;
$symbol |= ($charList[3] & 0x3F) << 12;
$symbol |= ($charList[4] & 0x3F) << 6;
$symbol |= ($charList[5] & 0x3F);
$context
    ->setNewToken(TokenType::SYMBOL)
    ->setTokenAttribute(TokenAttribute::UNICODE_CHAR, $symbol);

/** @lexOnError */
if ($context->getBuffer()->isEnd()) {
    return false;
}
$context->getBuffer()->nextSymbol();
$context->setNewToken(TokenType::INVALID_BYTES);
return true;
