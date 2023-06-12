<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Grammar;

use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\RegExp\AST\NodeType;

class ProductionTranslationScheme
{
    public function __construct(
        private Tree $tree,
    ) {
    }

    /**
     * @throws Exception
     */
    public function applyActions(Production $production): void
    {
        $header = $production->getHeaderShortcut();
        $symbols = $production->getSymbolListShortcut();
        $hash = "{$production->getHeader()->getSymbolId()}.{$production->getIndex()}";
        switch ($hash) {
            /**
             * Root production.
             */
            case SymbolType::NT_ROOT . ".0":
                // [SymbolType::NT_PARTS, SymbolType::T_EOI]
                $rootNode = $this
                    ->tree
                    ->getNode($symbols[0]['s.alternatives_node']);
                $this
                    ->tree
                    ->setRootNode($rootNode);
                break;

            /**
             * Alternative patterns.
             */
            case SymbolType::NT_PARTS . ".0":
                // [SymbolType::NT_PART, SymbolType::NT_ALT_PARTS]
                $header['s.alternatives_node'] = $symbols[1]['s.alternatives_node'];
                break;

            case SymbolType::NT_ALT_PARTS . ".0":
            case SymbolType::NT_ALT_PARTS_TAIL . ".0":
                // [SymbolType::NT_ALT_SEPARATOR, SymbolType::NT_PART, SymbolType::NT_ALT_PARTS_TAIL]
                $header['s.alternatives_node'] = $symbols[2]['s.alternatives_node'];
                break;

            case SymbolType::NT_ALT_PARTS . ".1":
                // []
                $header['s.alternatives_node'] = $header['i.alternative_node'];
                break;

            case SymbolType::NT_ALT_PARTS_TAIL . ".1":
                // []
                $header['s.alternatives_node'] = $header['i.alternatives_node'];
                $alternativeNode = $this
                    ->tree
                    ->getNode($header['i.alternative_node']);
                $this
                    ->tree
                    ->getNode($header['i.alternatives_node'])
                    ->addChild($alternativeNode);
                break;

            /**
             * Repeatable pattern.
             */
            case SymbolType::NT_PART . ".0":
                // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS]
                $header['s.alternative_node'] = $symbols[1]['s.concatenate_node'];
                break;

            case SymbolType::NT_PART . ".1":
                // []
                $header['s.alternative_node'] = $this
                    ->tree
                    ->createNode(NodeType::EMPTY)
                    ->getId();
                break;

            case SymbolType::NT_MORE_ITEMS . ".0":
                // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL]
                $header['s.concatenate_node'] = $symbols[1]['s.concatenate_node'];
                break;

            case SymbolType::NT_MORE_ITEMS . ".1":
                // []
                $header['s.concatenate_node'] = $header['i.concatenable_node'];
                break;

            case SymbolType::NT_MORE_ITEMS_TAIL . ".0":
                // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL]
                $header['s.concatenate_node'] = $symbols[1]['s.concatenate_node'];
                break;

            case SymbolType::NT_MORE_ITEMS_TAIL . ".1":
                // []
                $header['s.concatenate_node'] = $header['i.concatenate_node'];
                $concatenableNode = $this
                    ->tree
                    ->getNode($header['i.concatenable_node']);
                $this
                    ->tree
                    ->getNode($header['i.concatenate_node'])
                    ->addChild($concatenableNode);
                break;

            case SymbolType::NT_ITEM . ".0":
                // [SymbolType::NT_ASSERT]
                $header['s.concatenable_node'] = $symbols[0]['s.assert_node'];
                break;

            case SymbolType::NT_ITEM . ".1":
                // [SymbolType::NT_ITEM_BODY, SymbolType::NT_ITEM_QUANT]
                $min = $symbols[1]['s.min'];
                $max = $symbols[1]['s.max'];
                $isMaxInfinite = $symbols[1]['s.is_max_infinite'];
                $shouldNotRepeat = 1 == $min && 1 == $max && !$isMaxInfinite;
                if ($shouldNotRepeat) {
                    $header['s.concatenable_node'] = $symbols[0]['s.repeatable_node'];
                    break;
                }
                $repeatableNode = $this
                    ->tree
                    ->getNode($symbols[0]['s.repeatable_node']);
                $repeatNode = $this
                    ->tree
                    ->createNode(NodeType::REPEAT)
                    ->setAttribute('min', $min)
                    ->setAttribute('max', $max)
                    ->setAttribute('is_max_infinite', $isMaxInfinite)
                    ->addChild($repeatableNode);
                $header['s.concatenable_node'] = $repeatNode->getId();
                break;

            case SymbolType::NT_ITEM_BODY . ".0":
                // [SymbolType::NT_GROUP]
                $header['s.repeatable_node'] = $symbols[0]['s.group_node'];
                break;

            case SymbolType::NT_ITEM_BODY . ".1":
                // [SymbolType::NT_CLASS_]
                $header['s.repeatable_node'] = $symbols[0]['s.class_node'];
                break;

            case SymbolType::NT_ITEM_BODY . ".2":
                // [SymbolType::NT_SYMBOL]
                $header['s.repeatable_node'] = $symbols[0]['s.symbol_node'];
                break;

            case SymbolType::NT_CLASS . ".0":
                // [SymbolType::NT_CLASS_START, SymbolType::NT_CLASS_BODY, SymbolType::NT_CLASS_END]
                $header['s.class_node'] = $symbols[1]['s.class_node'];
                break;

            case SymbolType::NT_CLASS_BODY . ".0":
                // [SymbolType::NT_CLASS_INVERTER, SymbolType::NT_FIRST_INV_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS]
                $header['s.class_node'] = $symbols[2]['s.class_node'];
                break;

            case SymbolType::NT_CLASS_BODY . ".1":
                // [SymbolType::NT_FIRST_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS]
                $header['s.class_node'] = $symbols[1]['s.class_node'];
                break;

            case SymbolType::NT_FIRST_CLASS_ITEM . ".0":
                // [SymbolType::NT_FIRST_CLASS_SYMBOL, SymbolType::NT_RANGE]
                $header['s.symbol_node'] = $symbols[1]['s.symbol_node'];
                break;

            case SymbolType::NT_FIRST_CLASS_ITEM . ".1":
            case SymbolType::NT_FIRST_INV_CLASS_ITEM . ".1":
                // [SymbolType::NT_ESC_CLASS_SYMBOL, SymbolType::NT_RANGE]
                $header['s.symbol_node'] = $symbols[1]['s.symbol_node'];
                break;

            case SymbolType::NT_FIRST_INV_CLASS_ITEM . ".0":
                // [SymbolType::NT_FIRST_INV_CLASS_SYMBOL, SymbolType::NT_RANGE]
                $header['s.symbol_node'] = $symbols[1]['s.symbol_node'];
                break;

            case SymbolType::NT_CLASS_ITEM . ".0":
                // [SymbolType::NT_CLASS_SYMBOL, SymbolType::NT_RANGE]
                $header['s.symbol_node'] = $symbols[1]['s.symbol_node'];
                break;

            case SymbolType::NT_ESC_CLASS_SYMBOL . ".0":
                // [SymbolType::NT_ESC, SymbolType::NT_CLASS_ESC_SEQUENCE]
                $header['s.symbol_node'] = $symbols[1]['s.escape_node'];
                break;

            case SymbolType::NT_CLASS_ESC_SEQUENCE . ".0":
                // [SymbolType::NT_ESC_SIMPLE]
                $escapeNode = $this
                    ->tree
                    ->createNode(NodeType::ESC_SIMPLE)
                    ->setAttribute('code', $symbols[0]['s.code']);
                $header['s.escape_node'] = $escapeNode->getId();
                break;

            case SymbolType::NT_CLASS_ESC_SEQUENCE . ".1":
                // [SymbolType::NT_ESC_SPECIAL]
                $escapeNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL)
                    ->setAttribute('code', $symbols[0]['s.code']);
                $header['s.escape_node'] = $escapeNode->getId();
                break;

            case SymbolType::NT_CLASS_ESC_SEQUENCE . ".2":
                // [SymbolType::NT_ESC_NON_PRINTABLE]
                $header['s.escape_node'] = $symbols[0]['s.symbol_node'];
                break;

            case SymbolType::NT_CLASS_ESC_SEQUENCE . ".3":
                // [SymbolType::NT_ESC_PROP]
                $escapeNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL_PROP)
                    ->setAttribute('not', false)
                    ->setAttribute('name', $symbols[0]['s.name']);
                $header['s.escape_node'] = $escapeNode->getId();
                break;

            case SymbolType::NT_CLASS_ESC_SEQUENCE . ".4":
                // [SymbolType::NT_ESC_NOT_PROP]
                $escapeNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL_PROP)
                    ->setAttribute('not', true)
                    ->setAttribute('name', $symbols[0]['s.name']);
                $header['s.escape_node'] = $escapeNode->getId();
                break;

            case SymbolType::NT_CLASS_SYMBOL . ".0":
                // [SymbolType::NT_ESC_CLASS_SYMBOL]
                $header['s.symbol_node'] = $symbols[0]['s.symbol_node'];
                break;

            case SymbolType::NT_CLASS_SYMBOL . ".1":
                // [SymbolType::NT_UNESC_CLASS_SYMBOL]
                $symbolNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL)
                    ->setAttribute('code', $symbols[0]['s.code']);
                $header['s.symbol_node'] = $symbolNode->getId();
                break;

            case SymbolType::NT_RANGE . ".0":
                // [SymbolType::NT_RANGE_SEPARATOR, SymbolType::NT_CLASS_SYMBOL]
                $startRangeNode = $this
                    ->tree
                    ->getNode($header['i.symbol_node']);
                $finishRangeNode = $this
                    ->tree
                    ->getNode($symbols[1]['s.symbol_node']);
                $symbolNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL_RANGE)
                    ->addChild($startRangeNode)
                    ->addChild($finishRangeNode);
                $header['s.symbol_node'] = $symbolNode->getId();
                break;

            case SymbolType::NT_RANGE . ".1":
                // []
                $header['s.symbol_node'] = $header['i.symbol_node'];
                break;

            case SymbolType::NT_CLASS_ITEMS . ".0":
                // [SymbolType::NT_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS_TAIL]
                $header['s.class_node'] = $symbols[1]['i.class_node'];
                break;

            case SymbolType::NT_CLASS_ITEMS . ".1":
                // []
                $symbolNode = $this->tree->getNode($header['i.symbol_node']);
                $classNode = $header['i.not']
                    ? $this
                        ->tree
                        ->createNode(NodeType::SYMBOL_CLASS)
                        ->setAttribute('not', true)
                        ->addChild($symbolNode)
                    : $symbolNode;
                $header['s.class_node'] = $classNode->getId();
                break;

            case SymbolType::NT_CLASS_ITEMS_TAIL . ".0":
                // [SymbolType::NT_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS_TAIL]
                // TODO: Wierd SDD, maybe bug? Write test.
            case SymbolType::NT_CLASS_ITEMS_TAIL . ".1":
                // []
                $header['s.class_node'] = $header['i.class_node'];
                break;


            case SymbolType::NT_SYMBOL . ".0":
                // [SymbolType::NT_SYMBOL_ANY]
                $symbolNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL_ANY);
                $header['s.symbol_node'] = $symbolNode->getId();
                break;

            case SymbolType::NT_SYMBOL . ".1":
                // [SymbolType::NT_ESC_SYMBOL]
                $header['s.symbol_node'] = $symbols[0]['s.escape_node'];
                break;

            case SymbolType::NT_SYMBOL . ".2":
                // [SymbolType::NT_UNESC_SYMBOL]
                $symbolNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL)
                    ->setAttribute('code', $symbols[0]['s.code']);
                $header['s.symbol_node'] = $symbolNode->getId();
                break;

            case SymbolType::NT_GROUP . ".0":
                // [SymbolType::NT_GROUP_START, SymbolType::NT_PARTS, SymbolType::NT_GROUP_END]
                $header['s.group_node'] = $symbols[1]['s.alternatives_node'];
                break;

            case SymbolType::NT_ASSERT . ".0":
                // [SymbolType::NT_ASSERT_LINE_START]
                $assertNode = $this
                    ->tree
                    ->createNode(NodeType::ASSERT)
                    ->setAttribute('type', 'line_start');
                $header['s.assert_node'] = $assertNode->getId();
                break;

            case SymbolType::NT_ASSERT . ".1":
                // [SymbolType::NT_ASSERT_LINE_FINISH]
                $assertNode = $this
                    ->tree
                    ->createNode(NodeType::ASSERT)
                    ->setAttribute('type', 'line_finish');
                $header['s.assert_node'] = $assertNode->getId();
                break;

            /**
             * Escaped patterns.
             */
            case SymbolType::NT_ESC_SYMBOL . ".0":
                // [SymbolType::NT_ESC, SymbolType::NT_ESC_SEQUENCE]
                $header['s.escape_node'] = $symbols[1]['s.escape_node'];
                break;

            case SymbolType::NT_ESC_SEQUENCE . ".0":
                // [SymbolType::NT_ESC_SIMPLE]
                $escapeNode = $this
                    ->tree
                    ->createNode(NodeType::ESC_SIMPLE)
                    ->setAttribute('code', $symbols[0]['s.code']);
                $header['s.escape_node'] = $escapeNode->getId();
                break;

            case SymbolType::NT_ESC_SEQUENCE . ".1":
                // [SymbolType::NT_ESC_SPECIAL]
                $escapeNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL)
                    ->setAttribute('code', $symbols[0]['s.code']);
                $header['s.escape_node'] = $escapeNode->getId();
                break;

            case SymbolType::NT_ESC_SEQUENCE . ".2":
                // [SymbolType::NT_ESC_NON_PRINTABLE]
                $header['s.escape_node'] = $symbols[0]['s.symbol_node'];
                break;

            case SymbolType::NT_ESC_SEQUENCE . ".3":
                // [SymbolType::NT_ESC_PROP]
                $escapeNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL_PROP)
                    ->setAttribute('not', false)
                    ->setAttribute('name', $symbols[0]['s.name']);
                $header['s.escape_node'] = $escapeNode->getId();
                break;

            case SymbolType::NT_ESC_SEQUENCE . ".4":
                // [SymbolType::NT_ESC_NOT_PROP]
                $escapeNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL_PROP)
                    ->setAttribute('not', true)
                    ->setAttribute('name', $symbols[0]['s.name']);
                $header['s.escape_node'] = $escapeNode->getId();
                break;

            case SymbolType::NT_ESC_PROP . ".0":
                // [SymbolType::NT_ESC_PROP_MARKER, SymbolType::NT_PROP]
            case SymbolType::NT_ESC_NOT_PROP . ".0":
                // [SymbolType::NT_ESC_NOT_PROP_MARKER, SymbolType::NT_PROP]
                $header['s.name'] = $symbols[1]['s.name'];
                break;

            case SymbolType::NT_PROP . ".0":
                // [SymbolType::NT_PROP_SHORT]
            case SymbolType::NT_PROP . ".1":
                // [SymbolType::NT_PROP_FULL]
                $header['s.name'] = $symbols[0]['s.name'];
                break;


            case SymbolType::NT_PROP_SHORT . ".0":
                // [SymbolType::NT_NOT_PROP_START]
                $header['s.name'] = [$symbols[0]['s.code']];
                break;

            case SymbolType::NT_PROP_FULL . ".0":
                // [SymbolType::NT_PROP_START, SymbolType::NT_PROP_NAME, SymbolType::NT_PROP_FINISH]
                $header['s.name'] = $symbols[1]['s.name'];
                break;

            case SymbolType::NT_PROP_NAME . ".0":
                // [SymbolType::NT_PROP_NAME_PART]
                $header['s.name'] = $symbols[0]['s.name'];
                break;

            case SymbolType::NT_PROP_NAME_PART . ".0":
                // [SymbolType::NT_NOT_PROP_FINISH, SymbolType::NT_PROP_NAME_PART]
                $header['s.name'] = $symbols[1]['s.name'];
                break;

            case SymbolType::NT_PROP_NAME_PART . ".1":
                // []
                $header['s.name'] = $header['i.name'];
                break;

            case SymbolType::NT_ESC_NON_PRINTABLE . ".0":
                // [SymbolType::NT_ESC_CTL]
            case SymbolType::NT_ESC_NON_PRINTABLE . ".1":
                // [SymbolType::NT_ESC_OCT]
            case SymbolType::NT_ESC_NON_PRINTABLE . ".2":
                // [SymbolType::NT_ESC_HEX]
            case SymbolType::NT_ESC_NON_PRINTABLE . ".3":
                // [SymbolType::NT_ESC_UNICODE]
                $header['s.symbol_node'] = $symbols[0]['s.symbol_node'];
                break;

            case SymbolType::NT_ESC_CTL . ".0":
                // [SymbolType::NT_ESC_CTL_MARKER, SymbolType::NT_ESC_CTL_CODE]
                $symbolNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL_CTL)
                    ->setAttribute('code', $symbols[1]['s.code']);
                $header['s.symbol_node'] = $symbolNode->getId();
                break;

            case SymbolType::NT_ESC_CTL_CODE . ".0":
                // [SymbolType::NT_PRINTABLE_ASCII]
                $header['s.code'] = $symbols[0]['s.code'];
                break;

            case SymbolType::NT_ESC_OCT . ".0":
                // [SymbolType::NT_ESC_OCT_SHORT]
            case SymbolType::NT_ESC_OCT . ".1":
                // [SymbolType::NT_ESC_OCT_LONG]
                $symbolNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL)
                    ->setAttribute('code', $symbols[0]['s.code']);
                $header['s.symbol_node'] = $symbolNode->getId();
                break;

            case SymbolType::NT_ESC_OCT_SHORT . ".0":
                // [SymbolType::NT_ESC_OCT_SHORT_MARKER]
                $octNumber = $symbols[0]['s.oct_digit'];
                $header['s.code'] = octdec($octNumber);
                break;

            case SymbolType::NT_ESC_OCT_SHORT_MARKER . ".0":
                // [SymbolType::T_DIGIT_ZERO]
                $header['s.oct_digit'] = $symbols[0]['s.oct_digit'];
                break;

            case SymbolType::NT_ESC_OCT_LONG . ".0":
                // [SymbolType::NT_ESC_OCT_LONG_MARKER, SymbolType::NT_ESC_OCT_LONG_NUM]
                $header['s.code'] = $symbols[1]['s.code'];
                break;

            case SymbolType::NT_ESC_OCT_LONG_NUM . ".0":
                // [SymbolType::NT_ESC_NUM_START, SymbolType::NT_OCT, SymbolType::NT_ESC_NUM_FINISH]
                $header['s.code'] = $symbols[1]['s.number_value'];
                break;

            case SymbolType::NT_ESC_UNICODE . ".0":
                // [SymbolType::NT_ESC_UNICODE_MARKER, SymbolType::NT_ESC_UNICODE_NUM]
                $header['s.symbol_node'] = $symbols[1]['s.symbol_node'];
                break;

            case SymbolType::NT_ESC_UNICODE_NUM . ".0":
                // [4 x SymbolType::NT_HEX_DIGIT]
                $hexNumberString =
                    $symbols[0]['s.hex_digit'] .
                    $symbols[1]['s.hex_digit'] .
                    $symbols[2]['s.hex_digit'] .
                    $symbols[3]['s.hex_digit'];
                $hexNumber = hexdec($hexNumberString);
                $symbolNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL)
                    ->setAttribute('code', $hexNumber);
                $header['s.symbol_node'] = $symbolNode->getId();
                break;

            case SymbolType::NT_ESC_HEX . ".0":
                // [SymbolType::NT_ESC_HEX_MARKER, SymbolType::NT_ESC_HEX_NUM]
                $symbolNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL)
                    ->setAttribute('code', $symbols[1]['s.code']);
                $header['s.symbol_node'] = $symbolNode->getId();
                break;

            case SymbolType::NT_ESC_HEX_NUM . ".0":
                // [SymbolType::NT_ESC_HEX_SHORT_NUM]
            case SymbolType::NT_ESC_HEX_NUM . ".1":
                // [SymbolType::NT_ESC_HEX_LONG_NUM]
                $header['s.code'] = $symbols[0]['s.code'];
                break;

            case SymbolType::NT_ESC_HEX_SHORT_NUM . ".0":
                // [SymbolType::NT_HEX_DIGIT, SymbolType::NT_HEX_DIGIT]
                $hexNumberString = $symbols[0]['s.hex_digit'] . $symbols[1]['s.hex_digit'];
                $header['s.code'] = hexdec($hexNumberString);
                break;

            case SymbolType::NT_ESC_HEX_LONG_NUM . ".0":
                // [SymbolType::NT_ESC_NUM_START, SymbolType::NT_HEX, SymbolType::NT_ESC_NUM_FINISH]
                $header['s.code'] = $symbols[1]['s.number_value'];
                break;

            /**
             * Repeatable pattern quantifier.
             */
            case SymbolType::NT_ITEM_QUANT . ".0":
                // [SymbolType::NT_ITEM_OPT]
                $header['s.min'] = 0;
                $header['s.max'] = 1;
                $header['s.is_max_infinite'] = false;
                break;

            case SymbolType::NT_ITEM_QUANT . ".1":
                // [SymbolType::NT_ITEM_QUANT_STAR]
                $header['s.min'] = 0;
                $header['s.max'] = 0;
                $header['s.is_max_infinite'] = true;
                break;

            case SymbolType::NT_ITEM_QUANT . ".2":
                // [SymbolType::NT_ITEM_QUANT_PLUS]
                $header['s.min'] = 1;
                $header['s.max'] = 0;
                $header['s.is_max_infinite'] = true;
                break;

            case SymbolType::NT_ITEM_QUANT . ".3":
                // [SymbolType::NT_LIMIT]
                $header['s.min'] = $symbols[0]['s.min'];
                $header['s.max'] = $symbols[0]['s.max'];
                $header['s.is_max_infinite'] = $symbols[0]['s.is_max_infinite'];
                break;

            case SymbolType::NT_ITEM_QUANT . ".4":
                // []
                $header['s.min'] = 1;
                $header['s.max'] = 1;
                $header['s.is_max_infinite'] = false;
                break;

            case SymbolType::NT_LIMIT . ".0":
                // [SymbolType::NT_LIMIT_START, SymbolType::NT_MIN, SymbolType::NT_OPT_MAX, SymbolType::NT_LIMIT_END]
                $header['s.min'] = $symbols[1]['s.number_value'];
                $header['s.max'] = $symbols[2]['s.number_value'];
                $header['s.is_max_infinite'] = $symbols[2]['s.is_infinite'];
                break;

            case SymbolType::NT_MIN . ".0":
                // [SymbolType::NT_DEC]
                $header['s.number_value'] = $symbols[0]['s.number_value'];
                break;

            case SymbolType::NT_MAX . ".0":
                // [SymbolType::NT_DEC]
                $header['s.number_value'] = $symbols[0]['s.number_value'];
                $header['s.is_infinite'] = false;
                break;

            case SymbolType::NT_MAX . ".1":
                // []
                $header['s.number_value'] = 0;
                $header['s.is_infinite'] = true;
                break;

            case SymbolType::NT_OPT_MAX . ".0":
                // [SymbolType::NT_LIMIT_SEPARATOR, SymbolType::NT_MAX]
                $header['s.number_value'] = $symbols[1]['s.number_value'];
                $header['s.is_infinite'] = $symbols[1]['s.is_infinite'];
                break;

            case SymbolType::NT_OPT_MAX . ".1":
                // []
                $header['s.number_value'] = $header['i.min'];
                $header['s.is_infinite'] = false;
                break;

            /**
             * Decimal numbers.
             */
            case SymbolType::NT_DEC . ".0":
                // [SymbolType::NT_DEC_DIGIT, SymbolType::NT_OPT_DEC]
                $number = $symbols[0]['s.dec_digit'] . $symbols[1]['s.dec_number_tail'];
                $header['s.number_value'] = (int) $number;
                break;

            case SymbolType::NT_OPT_DEC . ".0":
                // [SymbolType::NT_DEC_DIGIT, SymbolType::NT_OPT_DEC]
                $numberTail = $symbols[0]['s.dec_digit'] . $symbols[1]['s.dec_number_tail'];
                $header['s.dec_number_tail'] = $numberTail;
                break;

            case SymbolType::NT_OPT_DEC . ".1":
                // []
                $header['s.dec_number_tail'] = '';
                break;

            case SymbolType::NT_DEC_DIGIT . ".0":
                // [SymbolType::T_DIGIT_ZERO]
            case SymbolType::NT_DEC_DIGIT . ".1":
                // [SymbolType::T_DIGIT_OCT]
            case SymbolType::NT_DEC_DIGIT . ".2":
                // [SymbolType::T_DIGIT_DEC]
                $header['s.dec_digit'] = $symbols[0]['s.dec_digit'];
                break;

            /**
             * Octal numbers.
             */
            case SymbolType::NT_OCT . ".0":
                // [SymbolType::NT_DEC_DIGIT, SymbolType::NT_OPT_DEC]
                $number = $symbols[0]['s.oct_digit'] . $symbols[1]['s.oct_number_tail'];
                $header['s.number_value'] = octdec($number);
                break;

            case SymbolType::NT_OPT_OCT . ".0":
                // [SymbolType::NT_OCT_DIGIT, SymbolType::NT_OPT_OCT]
                $numberTail = $symbols[0]['s.oct_digit'] . $symbols[1]['s.oct_number_tail'];
                $header['s.oct_number_tail'] = $numberTail;
                break;

            case SymbolType::NT_OPT_OCT . ".1":
                // []
                $header['s.oct_number_tail'] = '';
                break;

            case SymbolType::NT_OCT_DIGIT . ".0":
                // [SymbolType::T_DIGIT_ZERO]
            case SymbolType::NT_OCT_DIGIT . ".1":
                // [SymbolType::T_DIGIT_OCT]
                $header['s.oct_digit'] = $symbols[0]['s.oct_digit'];
                break;

            /**
             * Hexadecimal numbers.
             */
            case SymbolType::NT_HEX . ".0":
                // [SymbolType::NT_HEX_DIGIT, SymbolType::NT_OPT_HEX]
                $hexNumber = $symbols[0]['s.hex_digit'] . $symbols[1]['s.hex_number_tail'];
                $header['s.number_value'] = hexdec($hexNumber);
                break;

            case SymbolType::NT_OPT_HEX . ".0":
                // [SymbolType::NT_HEX_DIGIT, SymbolType::NT_OPT_HEX]
                $numberTail = $symbols[0]['s.hex_digit'] . $symbols[1]['s.hex_number_tail'];
                $header['s.hex_number_tail'] = $numberTail;
                break;

            case SymbolType::NT_OPT_HEX . ".1":
                // []
                $header['s.hex_number_tail'] = '';
                break;

            case SymbolType::NT_HEX_DIGIT . ".0":
                // [SymbolType::T_DIGIT_ZERO]
            case SymbolType::NT_HEX_DIGIT . ".1":
                // [SymbolType::T_DIGIT_OCT]
            case SymbolType::NT_HEX_DIGIT . ".2":
                // [SymbolType::T_DIGIT_DEC]
            case SymbolType::NT_HEX_DIGIT . ".3":
                // [SymbolType::T_SMALL_C]
            case SymbolType::NT_HEX_DIGIT . ".4":
                // [SymbolType::T_OTHER_HEX_LETTER]
                $header['s.hex_digit'] = $symbols[0]['s.hex_digit'];
                break;

            /**
             * Printable ASCII symbols.
             */
            case SymbolType::NT_PRINTABLE_ASCII . ".0":
                // [SymbolType::NT_META_CHAR]
            case SymbolType::NT_PRINTABLE_ASCII . ".1":
                // [SymbolType::NT_DEC_DIGIT]
            case SymbolType::NT_PRINTABLE_ASCII . ".2":
                // [SymbolType::NT_ASCII_LETTER]
            case SymbolType::NT_PRINTABLE_ASCII . ".3":
                // [SymbolType::NT_PRINTABLE_ASCII_OTHER]
                $header['s.code'] = $symbols[0]['s.code'];
                break;

            /**
             * Non-terminal symbols with code.
             */
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".0":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".0":
            case SymbolType::NT_ESC_SPECIAL . ".10":
            case SymbolType::NT_NOT_PROP_START . ".15":
            case SymbolType::NT_NOT_PROP_FINISH . ".15":
            case SymbolType::NT_UNESC_SYMBOL . ".6":
            case SymbolType::NT_META_CHAR . ".11":
                // [SymbolType::T_RIGHT_SQUARE_BRACKET]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".1":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".1":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".0":
            case SymbolType::NT_ESC_SPECIAL . ".0":
            case SymbolType::NT_NOT_PROP_START . ".0":
            case SymbolType::NT_NOT_PROP_FINISH . ".0":
            case SymbolType::NT_META_CHAR . ".0":
                // [SymbolType::T_DOLLAR]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".2":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".2":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".1":
            case SymbolType::NT_ESC_SPECIAL . ".1":
            case SymbolType::NT_NOT_PROP_START . ".1":
            case SymbolType::NT_NOT_PROP_FINISH . ".1":
            case SymbolType::NT_META_CHAR . ".1":
                // [SymbolType::T_LEFT_BRACKET]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".3":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".3":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".2":
            case SymbolType::NT_ESC_SPECIAL . ".2":
            case SymbolType::NT_NOT_PROP_START . ".2":
            case SymbolType::NT_NOT_PROP_FINISH . ".2":
            case SymbolType::NT_META_CHAR . ".2":
                // [SymbolType::T_RIGHT_BRACKET]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".4":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".4":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".3":
            case SymbolType::NT_ESC_SPECIAL . ".3":
            case SymbolType::NT_NOT_PROP_START . ".3":
            case SymbolType::NT_NOT_PROP_FINISH . ".3":
            case SymbolType::NT_META_CHAR . ".3":
                // [SymbolType::T_STAR]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".5":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".5":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".4":
            case SymbolType::NT_ESC_SPECIAL . ".4":
            case SymbolType::NT_NOT_PROP_START . ".4":
            case SymbolType::NT_NOT_PROP_FINISH . ".4":
            case SymbolType::NT_META_CHAR . ".4":
                // [SymbolType::T_PLUS]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".6":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".6":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".5":
            case SymbolType::NT_ESC_SPECIAL . ".5":
            case SymbolType::NT_NOT_PROP_START . ".5":
            case SymbolType::NT_NOT_PROP_FINISH . ".5":
            case SymbolType::NT_UNESC_SYMBOL . ".0":
            case SymbolType::NT_META_CHAR . ".5":
                // [SymbolType::T_COMMA]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".7":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".7":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".6":
            case SymbolType::NT_ESC_SPECIAL . ".7":
            case SymbolType::NT_NOT_PROP_START . ".11":
            case SymbolType::NT_NOT_PROP_FINISH . ".11":
            case SymbolType::NT_META_CHAR . ".8":
                // [SymbolType::T_QUESTION]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".8":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".8":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".7":
            case SymbolType::NT_ESC_SPECIAL . ".8":
            case SymbolType::NT_NOT_PROP_START . ".13":
            case SymbolType::NT_NOT_PROP_FINISH . ".13":
            case SymbolType::NT_META_CHAR . ".9":
                // [SymbolType::T_LEFT_SQUARE_BRACKET]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".9":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".9":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".9":
            case SymbolType::NT_ESC_SPECIAL . ".12":
            case SymbolType::NT_NOT_PROP_FINISH . ".22":
            case SymbolType::NT_META_CHAR . ".13":
                // [SymbolType::T_LEFT_CURLY_BRACKET]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".10":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".10":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".10":
            case SymbolType::NT_ESC_SPECIAL . ".13":
            case SymbolType::NT_NOT_PROP_START . ".22":
            case SymbolType::NT_NOT_PROP_FINISH . ".23":
            case SymbolType::NT_META_CHAR . ".14":
                // [SymbolType::T_VERTICAL_LINE]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".11":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".11":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".11":
            case SymbolType::NT_ESC_SPECIAL . ".14":
            case SymbolType::NT_NOT_PROP_START . ".23":
            case SymbolType::NT_UNESC_SYMBOL . ".12":
            case SymbolType::NT_META_CHAR . ".15":
                // [SymbolType::T_RIGHT_CURLY_BRACKET]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".12":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".12":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".12":
            case SymbolType::NT_ESC_SPECIAL . ".15":
            case SymbolType::NT_NOT_PROP_START . ".24":
            case SymbolType::NT_NOT_PROP_FINISH . ".24":
            case SymbolType::NT_UNESC_SYMBOL . ".13":
                // [SymbolType::T_CTL_ASCII]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".13":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".13":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".13":
            case SymbolType::NT_ESC_SIMPLE . ".1":
            case SymbolType::NT_NOT_PROP_START . ".25":
            case SymbolType::NT_NOT_PROP_FINISH . ".25":
            case SymbolType::NT_UNESC_SYMBOL . ".14":
            case SymbolType::NT_ASCII_LETTER . ".7":
                // [SymbolType::T_OTHER_HEX_LETTER]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".14":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".14":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".14":
            case SymbolType::NT_ESC_SIMPLE . ".0":
            case SymbolType::NT_NOT_PROP_START . ".26":
            case SymbolType::NT_NOT_PROP_FINISH . ".26":
            case SymbolType::NT_UNESC_SYMBOL . ".15":
            case SymbolType::NT_ASCII_LETTER . ".6":
                // [SymbolType::T_OTHER_ASCII_LETTER]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".15":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".15":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".15":
            case SymbolType::NT_ESC_SPECIAL . ".16":
            case SymbolType::NT_NOT_PROP_START . ".27":
            case SymbolType::NT_NOT_PROP_FINISH . ".27":
            case SymbolType::NT_UNESC_SYMBOL . ".16":
            case SymbolType::NT_PRINTABLE_ASCII_OTHER . ".0":
                // [SymbolType::T_PRINTABLE_ASCII_OTHER]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".16":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".16":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".16":
            case SymbolType::NT_ESC_SPECIAL . ".17":
            case SymbolType::NT_NOT_PROP_START . ".28":
            case SymbolType::NT_NOT_PROP_FINISH . ".28":
            case SymbolType::NT_UNESC_SYMBOL . ".17":
                // [SymbolType::T_OTHER_ASCII]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".17":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".17":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".17":
            case SymbolType::NT_ESC_SPECIAL . ".18":
            case SymbolType::NT_NOT_PROP_START . ".29":
            case SymbolType::NT_NOT_PROP_FINISH . ".29":
            case SymbolType::NT_UNESC_SYMBOL . ".18":
                // [SymbolType::T_NOT_ASCII]
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".18":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".8":
            case SymbolType::NT_ESC_SPECIAL . ".11":
            case SymbolType::NT_NOT_PROP_START . ".16":
            case SymbolType::NT_NOT_PROP_FINISH . ".16":
            case SymbolType::NT_META_CHAR . ".12":
                // [SymbolType::T_CIRCUMFLEX]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".18":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".19":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".18":
            case SymbolType::NT_NOT_PROP_START . ".12":
            case SymbolType::NT_NOT_PROP_FINISH . ".12":
            case SymbolType::NT_UNESC_SYMBOL . ".5":
            case SymbolType::NT_ASCII_LETTER . ".0":
                // [SymbolType::T_CAPITAL_P]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".19":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".20":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".19":
            case SymbolType::NT_NOT_PROP_START . ".17":
            case SymbolType::NT_NOT_PROP_FINISH . ".17":
            case SymbolType::NT_UNESC_SYMBOL . ".7":
            case SymbolType::NT_ASCII_LETTER . ".1":
                // [SymbolType::T_SMALL_C]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".20":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".21":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".20":
            case SymbolType::NT_NOT_PROP_START . ".18":
            case SymbolType::NT_NOT_PROP_FINISH . ".18":
            case SymbolType::NT_UNESC_SYMBOL . ".8":
            case SymbolType::NT_ASCII_LETTER . ".2":
                // [SymbolType::T_SMALL_O]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".21":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".22":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".21":
            case SymbolType::NT_NOT_PROP_START . ".19":
            case SymbolType::NT_NOT_PROP_FINISH . ".19":
            case SymbolType::NT_UNESC_SYMBOL . ".9":
            case SymbolType::NT_ASCII_LETTER . ".3":
                // [SymbolType::T_SMALL_P]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".22":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".23":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".22":
            case SymbolType::NT_NOT_PROP_START . ".20":
            case SymbolType::NT_NOT_PROP_FINISH . ".20":
            case SymbolType::NT_UNESC_SYMBOL . ".10":
            case SymbolType::NT_ASCII_LETTER . ".4":
                // [SymbolType::T_SMALL_U]
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".23":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".24":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".23":
            case SymbolType::NT_NOT_PROP_START . ".21":
            case SymbolType::NT_NOT_PROP_FINISH . ".21":
            case SymbolType::NT_UNESC_SYMBOL . ".11":
            case SymbolType::NT_ASCII_LETTER . ".5":
                // [SymbolType::T_SMALL_X]
            case SymbolType::NT_ESC_SIMPLE . ".2":
            case SymbolType::NT_NOT_PROP_START . ".9":
            case SymbolType::NT_NOT_PROP_FINISH . ".9":
            case SymbolType::NT_UNESC_SYMBOL . ".3":
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".25":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".26":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".25":
                // [SymbolType::T_DIGIT_OCT]
            case SymbolType::NT_ESC_SIMPLE . ".3":
            case SymbolType::NT_NOT_PROP_START . ".10":
            case SymbolType::NT_NOT_PROP_FINISH . ".10":
            case SymbolType::NT_UNESC_SYMBOL . ".4":
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".26":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".27":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".26":
                // [SymbolType::T_DIGIT_DEC]
            case SymbolType::NT_ESC_SPECIAL . ".6":
            case SymbolType::NT_NOT_PROP_START . ".6":
            case SymbolType::NT_NOT_PROP_FINISH . ".6":
            case SymbolType::NT_META_CHAR . ".6":
                // [SymbolType::T_HYPHEN]
            case SymbolType::NT_ESC_SPECIAL . ".9":
            case SymbolType::NT_NOT_PROP_START . ".14":
            case SymbolType::NT_NOT_PROP_FINISH . ".14":
            case SymbolType::NT_UNESC_SYMBOL . ".1":
            case SymbolType::NT_META_CHAR . ".10":
                // [SymbolType::T_BACKSLASH]
            case SymbolType::NT_NOT_PROP_START . ".7":
            case SymbolType::NT_NOT_PROP_FINISH . ".7":
            case SymbolType::NT_META_CHAR . ".7":
            case SymbolType::NT_ESC_SPECIAL . ".19":
                // [SymbolType::T_DOT]
            case SymbolType::NT_NOT_PROP_START . ".8":
            case SymbolType::NT_NOT_PROP_FINISH . ".8":
            case SymbolType::NT_UNESC_SYMBOL . ".2":
            case SymbolType::NT_FIRST_CLASS_SYMBOL . ".24":
            case SymbolType::NT_FIRST_INV_CLASS_SYMBOL . ".25":
            case SymbolType::NT_UNESC_CLASS_SYMBOL . ".24":
                // [SymbolType::T_DIGIT_ZERO]
                $header['s.code'] = $symbols[0]['s.code'];
                break;
        }
    }
}
