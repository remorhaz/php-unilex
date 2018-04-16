<?php
/**
 * @lexTargetClass TokenMatcher
 * @lexHeader
 */

namespace Remorhaz\UniLex\Example\Brainfuck\Grammar;

/**
 * @var \Remorhaz\UniLex\Lexer\TokenMatcherContextInterface $context
 *
 * @lexToken />/
 */
$context->setNewToken(TokenType::NEXT);

/** @lexToken /</ */
$context->setNewToken(TokenType::PREV);

/** @lexToken /\+/ */
$context->setNewToken(TokenType::INC);

/** @lexToken /-/ */
$context->setNewToken(TokenType::DEC);

/** @lexToken /\./ */
$context->setNewToken(TokenType::OUTPUT);

/** @lexToken /,/ */
$context->setNewToken(TokenType::INPUT);

/** @lexToken /\[/ */
$context->setNewToken(TokenType::LOOP);

/** @lexToken /]/ */
$context->setNewToken(TokenType::END_LOOP);
