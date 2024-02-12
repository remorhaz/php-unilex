<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Grammar;

use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\RegExp\AST\NodeType;

class SymbolTranslationScheme
{
    public function __construct(
        private readonly Tree $tree,
    ) {
    }

    /**
     * @param Production $production
     * @param int $symbolIndex
     * @throws Exception
     */
    public function applyActions(Production $production, int $symbolIndex): void
    {
        $header = $production->getHeaderShortcut();
        $symbols = $production->getSymbolListShortcut();
        $hash = "{$production->getHeader()->getSymbolId()}.{$production->getIndex()}.{$symbolIndex}";
        switch ($hash) {
            case SymbolType::NT_PARTS . ".0.1":
                // [SymbolType::NT_PART, SymbolType::NT_ALT_PARTS]
                // SymbolType::NT_ALT_PARTS
                $symbols[1]['i.alternative_node'] = $symbols[0]['s.alternative_node'];
                break;

            case SymbolType::NT_ALT_PARTS . ".0.1":
                // [SymbolType::NT_ALT_SEPARATOR, SymbolType::NT_PART, SymbolType::NT_ALT_PARTS_TAIL]
                // SymbolType::NT_PART
                $alternativesNode = $this
                    ->tree
                    ->createNode(NodeType::ALTERNATIVE)
                    ->addChild($this->tree->getNode($header['i.alternative_node']));
                $symbols[1]['i.alternatives_node'] = $alternativesNode->getId();
                break;

            case SymbolType::NT_ALT_PARTS . ".0.2":
                // SymbolType::NT_ALT_PARTS_TAIL
                $symbols[2]['i.alternatives_node'] = $symbols[1]['i.alternatives_node'];
                $symbols[2]['i.alternative_node'] = $symbols[1]['s.alternative_node'];
                break;

            case SymbolType::NT_ALT_PARTS_TAIL . ".0.1":
                // [SymbolType::NT_ALT_SEPARATOR, SymbolType::NT_PART, SymbolType::NT_ALT_PARTS_TAIL]
                // SymbolType::NT_PART
                $alternativesNode = $this
                    ->tree
                    ->getNode($header['i.alternatives_node'])
                    ->addChild($this->tree->getNode($header['i.alternative_node']));
                $symbols[1]['i.alternatives_node'] = $alternativesNode->getId();
                break;

            case SymbolType::NT_ALT_PARTS_TAIL . ".0.2":
                // SymbolType::NT_ALT_PARTS_TAIL
                $symbols[2]['i.alternatives_node'] = $symbols[1]['i.alternatives_node'];
                $symbols[2]['i.alternative_node'] = $symbols[1]['s.alternative_node'];
                break;

            case SymbolType::NT_PART . ".0.1":
                // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS]
                // SymbolType::NT_MORE_ITEMS
                $symbols[1]['i.concatenable_node'] = $symbols[0]['s.concatenable_node'];
                break;

            case SymbolType::NT_MORE_ITEMS . ".0.0":
                // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL]
                // SymbolType::NT_ITEM
                $concatenateNode = $this
                    ->tree
                    ->createNode(NodeType::CONCATENATE)
                    ->addChild($this->tree->getNode($header['i.concatenable_node']));
                $symbols[0]['i.concatenate_node'] = $concatenateNode->getId();
                break;

            case SymbolType::NT_MORE_ITEMS . ".0.1":
                // SymbolType::NT_MORE_ITEMS_TAIL
                $symbols[1]['i.concatenable_node'] = $symbols[0]['s.concatenable_node'];
                $symbols[1]['i.concatenate_node'] = $symbols[0]['i.concatenate_node'];
                break;

            case SymbolType::NT_MORE_ITEMS_TAIL . ".0.0":
                // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL]
                // SymbolType::NT_ITEM
                $concatenateNode = $this
                    ->tree
                    ->getNode($header['i.concatenate_node'])
                    ->addChild($this->tree->getNode($header['i.concatenable_node']));
                $symbols[0]['i.concatenate_node'] = $concatenateNode->getId();
                break;

            case SymbolType::NT_MORE_ITEMS_TAIL . ".0.1":
                // SymbolType::NT_MORE_ITEMS_TAIL
                $symbols[1]['i.concatenable_node'] = $symbols[0]['s.concatenable_node'];
                $symbols[1]['i.concatenate_node'] = $symbols[0]['i.concatenate_node'];
                break;

            case SymbolType::NT_LIMIT . ".0.2":
                // [SymbolType::NT_LIMIT_START, SymbolType::NT_MIN, SymbolType::NT_OPT_MAX, SymbolType::NT_LIMIT_END]
                // SymbolType::NT_OPT_MAX
                $symbols[2]['i.min'] = $symbols[1]['s.number_value'];
                break;

            case SymbolType::NT_PROP_NAME . ".0.0":
                // [SymbolType::NT_PROP_NAME_PART]
                // SymbolType::NT_PROP_NAME_PART
                $symbols[0]['i.name'] = [];
                break;

            case SymbolType::NT_PROP_NAME_PART . ".0.1":
                // [SymbolType::NT_NOT_PROP_FINISH, SymbolType::NT_PROP_NAME_PART]
                // SymbolType::NT_PROP_NAME_PART
                $symbols[1]['i.name'] = array_merge($header['i.name'], [$symbols[0]['s.code']]);
                break;

            case SymbolType::NT_CLASS_BODY . ".0.2":
                // [SymbolType::NT_CLASS_INVERTER, SymbolType::NT_FIRST_INV_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS]
                // SymbolType::NT_CLASS_ITEMS
                $symbols[2]['i.not'] = true;
                $symbols[2]['i.symbol_node'] = $symbols[1]['s.symbol_node'];
                break;

            case SymbolType::NT_CLASS_BODY . ".1.1":
                // [SymbolType::NT_FIRST_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS]
                // SymbolType::NT_CLASS_ITEMS
                $symbols[1]['i.not'] = false;
                $symbols[1]['i.symbol_node'] = $symbols[0]['s.symbol_node'];
                break;

            case SymbolType::NT_FIRST_CLASS_ITEM . ".0.1":
            case SymbolType::NT_FIRST_INV_CLASS_ITEM . ".0.1":
                // [SymbolType::NT_FIRST_CLASS_SYMBOL, SymbolType::NT_RANGE]
                // SymbolType::NT_RANGE
                $symbolNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL)
                    ->setAttribute('code', $symbols[0]['s.code']);
                $symbols[1]['i.symbol_node'] = $symbolNode->getId();
                break;

            case SymbolType::NT_FIRST_CLASS_ITEM . ".1.1":
            case SymbolType::NT_FIRST_INV_CLASS_ITEM . ".1.1":
                // [SymbolType::NT_ESC_CLASS_SYMBOL, SymbolType::NT_RANGE]
                // SymbolType::NT_RANGE
                $symbols[1]['i.symbol_node'] = $symbols[0]['s.symbol_node'];
                break;

            case SymbolType::NT_CLASS_ITEMS . ".0.1":
                // [SymbolType::NT_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS_TAIL]
                // SymbolType::NT_CLASS_ITEMS_TAIL
                $classNode = $this
                    ->tree
                    ->createNode(NodeType::SYMBOL_CLASS)
                    ->setAttribute('not', $header['i.not'])
                    ->addChild($this->tree->getNode($header['i.symbol_node']))
                    ->addChild($this->tree->getNode($symbols[0]['s.symbol_node']));
                $symbols[1]['i.class_node'] = $classNode->getId();
                break;

            case SymbolType::NT_CLASS_ITEMS_TAIL . ".0.1":
                // [SymbolType::NT_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS_TAIL]
                // SymbolType::NT_CLASS_ITEMS_TAIL
                $classNode = $this
                    ->tree
                    ->getNode($header['i.class_node'])
                    ->addChild($this->tree->getNode($symbols[0]['s.symbol_node']));
                $symbols[1]['i.class_node'] = $classNode->getId();
                break;

            case SymbolType::NT_CLASS_ITEM . ".0.1":
                // [SymbolType::NT_CLASS_SYMBOL, SymbolType::NT_RANGE]
                // SymbolType::NT_RANGE
                $symbols[1]['i.symbol_node'] = $symbols[0]['s.symbol_node'];
                break;
        }
    }
}
