<?php

namespace Remorhaz\UniLex\RegExp\Grammar;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\SyntaxTree\Node;
use Remorhaz\UniLex\Parser\SyntaxTree\Tree;
use Remorhaz\UniLex\RegExp\AST\NodeType;

class SymbolTranslationScheme
{

    private $tree;

    private $production;

    private $symbolIndex;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     * @throws Exception
     */
    public function applyActions(ParsedProduction $production, int $symbolIndex): void
    {
        $this->setContext($production, $symbolIndex);

        $headerId = $production
            ->getHeader()
            ->getSymbolId();
        $productionIndex = $production->getIndex();

        switch ("{$headerId}.{$productionIndex}.{$symbolIndex}") {
            case SymbolType::NT_PARTS . ".0.1":
                // [SymbolType::NT_PART, SymbolType::NT_ALT_PARTS]
                // SymbolType::NT_ALT_PARTS
                $this->inheritSymbolAttribute(0, 'i.alternative_node', 's.alternative_node');
                break;

            case SymbolType::NT_ALT_PARTS . ".0.1":
                // [SymbolType::NT_ALT_SEPARATOR, SymbolType::NT_PART, SymbolType::NT_ALT_PARTS_TAIL]
                // SymbolType::NT_PART
                $alternativesNode = $this
                    ->createNode(NodeType::ALTERNATIVE)
                    ->addChild($this->getNodeByHeaderAttribute('i.alternative_node'));
                $this->setSymbolAttribute('i.alternatives_node', $alternativesNode->getId());
                break;

            case SymbolType::NT_ALT_PARTS . ".0.2":
                // SymbolType::NT_ALT_PARTS_TAIL
                $this
                    ->inheritSymbolAttribute(1, 'i.alternatives_node')
                    ->inheritSymbolAttribute(1, 'i.alternative_node', 's.alternative_node');
                break;

            case SymbolType::NT_ALT_PARTS_TAIL . ".0.1":
                // [SymbolType::NT_ALT_SEPARATOR, SymbolType::NT_PART, SymbolType::NT_ALT_PARTS_TAIL]
                // SymbolType::NT_PART
                $alternativesNode = $this
                    ->getNodeByHeaderAttribute('i.alternatives_node')
                    ->addChild($this->getNodeByHeaderAttribute('i.alternative_node'));
                $this->setSymbolAttribute('i.alternatives_node', $alternativesNode->getId());
                break;

            case SymbolType::NT_ALT_PARTS_TAIL . ".0.2":
                // SymbolType::NT_ALT_PARTS_TAIL
                $this
                    ->inheritSymbolAttribute(1, 'i.alternatives_node')
                    ->inheritSymbolAttribute(1, 'i.alternative_node', 's.alternative_node');
                break;

            case SymbolType::NT_PART . ".0.1":
                // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS]
                // SymbolType::NT_MORE_ITEMS
                $this->inheritSymbolAttribute(0, 'i.concatenable_node', 's.concatenable_node');
                break;

            case SymbolType::NT_MORE_ITEMS . ".0.0":
                // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL]
                // SymbolType::NT_ITEM
                $concatenateNode = $this
                    ->createNode(NodeType::CONCATENATE)
                    ->addChild($this->getNodeByHeaderAttribute('i.concatenable_node'));
                $this->setSymbolAttribute('i.concatenate_node', $concatenateNode->getId());
                break;

            case SymbolType::NT_MORE_ITEMS . ".0.1":
                // SymbolType::NT_MORE_ITEMS_TAIL
                $this
                    ->inheritSymbolAttribute(0, 'i.concatenable_node', 's.concatenable_node')
                    ->inheritSymbolAttribute(0, 'i.concatenate_node');
                break;

            case SymbolType::NT_MORE_ITEMS_TAIL . ".0.0":
                // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL]
                // SymbolType::NT_ITEM
                $concatenateNode = $this
                    ->getNodeByHeaderAttribute('i.concatenate_node')
                    ->addChild($this->getNodeByHeaderAttribute('i.concatenable_node'));
                $this->setSymbolAttribute('i.concatenate_node', $concatenateNode->getId());
                break;

            case SymbolType::NT_MORE_ITEMS_TAIL . ".0.1":
                // SymbolType::NT_MORE_ITEMS_TAIL
                $this
                    ->inheritSymbolAttribute(0, 'i.concatenable_node', 's.concatenable_node')
                    ->inheritSymbolAttribute(0, 'i.concatenate_node');
                break;

            case SymbolType::NT_LIMIT . ".0.2":
                // [SymbolType::NT_LIMIT_START, SymbolType::NT_MIN, SymbolType::NT_OPT_MAX, SymbolType::NT_LIMIT_END]
                // SymbolType::NT_OPT_MAX
                $this->inheritSymbolAttribute(1, 'i.min', 's.number_value');
                break;

            case SymbolType::NT_PROP_NAME . ".0.0":
                // [SymbolType::NT_PROP_NAME_PART]
                // SymbolType::NT_PROP_NAME_PART
                $this->setSymbolAttribute('i.name', []);
                break;

            case SymbolType::NT_PROP_NAME_PART . ".0.1":
                // [SymbolType::NT_NOT_PROP_FINISH, SymbolType::NT_PROP_NAME_PART]
                // SymbolType::NT_PROP_NAME_PART
                $name = array_merge(
                    $this->getHeaderAttribute('i.name'),
                    [$this->getSymbolAttribute(0, 's.code')]
                );
                $this->setSymbolAttribute('i.name', $name);
                break;

            case SymbolType::NT_CLASS_BODY . ".0.2":
                // [SymbolType::NT_CLASS_INVERTER, SymbolType::NT_FIRST_INV_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS]
                // SymbolType::NT_CLASS_ITEMS
                $this
                    ->setSymbolAttribute('i.not', true)
                    ->inheritSymbolAttribute(1, 'i.symbol_node', 's.symbol_node');
                break;

            case SymbolType::NT_CLASS_BODY . ".1.1":
                // [SymbolType::NT_FIRST_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS]
                // SymbolType::NT_CLASS_ITEMS
                $this
                    ->setSymbolAttribute('i.not', false)
                    ->inheritSymbolAttribute(0, 'i.symbol_node', 's.symbol_node');
                break;

            case SymbolType::NT_FIRST_CLASS_ITEM . ".0.1":
            case SymbolType::NT_FIRST_INV_CLASS_ITEM . ".0.1":
                // [SymbolType::NT_FIRST_CLASS_SYMBOL, SymbolType::NT_RANGE]
                // SymbolType::NT_RANGE
                $symbolNode = $this
                    ->createNode(NodeType::SYMBOL)
                    ->setAttribute('code', $this->getSymbolAttribute(0, 's.code'));
                $this->setSymbolAttribute('i.symbol_node', $symbolNode->getId());
                break;

            case SymbolType::NT_FIRST_CLASS_ITEM . ".1.1":
            case SymbolType::NT_FIRST_INV_CLASS_ITEM . ".1.1":
                // [SymbolType::NT_ESC_CLASS_SYMBOL, SymbolType::NT_RANGE]
                // SymbolType::NT_RANGE
                $this->inheritSymbolAttribute(0, 'i.symbol_node', 's.symbol_node');
                break;

            case SymbolType::NT_CLASS_ITEMS . ".0.1":
                // [SymbolType::NT_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS_TAIL]
                // SymbolType::NT_CLASS_ITEMS_TAIL
                $classNode = $this
                    ->createNode(NodeType::SYMBOL_CLASS)
                    ->setAttribute('not', $this->getHeaderAttribute('i.not'))
                    ->addChild($this->getNodeByHeaderAttribute('i.symbol_node'))
                    ->addChild($this->getNodeBySymbolAttribute(0, 's.symbol_node'));
                $this->setSymbolAttribute('i.class_node', $classNode->getId());
                break;

            case SymbolType::NT_CLASS_ITEMS_TAIL . ".0.1":
                // [SymbolType::NT_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS_TAIL]
                // SymbolType::NT_CLASS_ITEMS_TAIL
                $classNode = $this
                    ->getNodeByHeaderAttribute('i.class_node')
                    ->addChild($this->getNodeBySymbolAttribute(0, 's.symbol_node'));
                $this->setSymbolAttribute('i.class_node', $classNode->getId());
                break;

            case SymbolType::NT_CLASS_ITEM . ".0.1":
                // [SymbolType::NT_CLASS_SYMBOL, SymbolType::NT_RANGE]
                // SymbolType::NT_RANGE
                $this->inheritSymbolAttribute(0, 'i.symbol_node', 's.symbol_node');
                break;
        }
    }

    /**
     * @param int $index
     * @param string $target
     * @param string|null $source
     * @return $this
     * @throws Exception
     */
    private function inheritSymbolAttribute(int $index, string $target, string $source = null)
    {
        $value = $this
            ->getProduction()
            ->getSymbol($index)
            ->getAttribute($source ?? $target);
        $this
            ->getProduction()
            ->getSymbol($this->getSymbolIndex())
            ->setAttribute($target, $value);
        return $this;
    }

    /**
     * @param string $attr
     * @return Node
     * @throws Exception
     */
    private function getNodeByHeaderAttribute(string $attr): Node
    {
        return $this
            ->tree
            ->getNode($this->getHeaderAttribute($attr));
    }

    /**
     * @param int $index
     * @param string $attr
     * @return Node
     * @throws Exception
     */
    private function getNodeBySymbolAttribute(int $index, string $attr): Node
    {
        return $this
            ->tree
            ->getNode($this->getSymbolAttribute($index, $attr));
    }

    /**
     * @param string $attr
     * @return mixed
     * @throws Exception
     */
    private function getHeaderAttribute(string $attr)
    {
        return $this
            ->getProduction()
            ->getHeader()
            ->getAttribute($attr);
    }

    /**
     * @param int $index
     * @param string $attr
     * @return mixed
     * @throws Exception
     */
    private function getSymbolAttribute(int $index, string $attr)
    {
        return $this
            ->getProduction()
            ->getSymbol($index)
            ->getAttribute($attr);
    }

    /**
     * @param string $attr
     * @param $value
     * @return $this
     * @throws Exception
     */
    private function setSymbolAttribute(string $attr, $value)
    {
        $this
            ->getProduction()
            ->getSymbol($this->getSymbolIndex())
            ->setAttribute($attr, $value);
        return $this;
    }

    private function createNode(string $name): Node
    {
        return $this
            ->tree
            ->createNode($name);
    }

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     */
    private function setContext(ParsedProduction $production, int $symbolIndex): void
    {
        $this->production = $production;
        $this->symbolIndex = $symbolIndex;
    }

    /**
     * @return ParsedProduction
     * @throws Exception
     */
    private function getProduction(): ParsedProduction
    {
        if (!isset($this->production)) {
            throw new Exception("No production defined in symbol translation scheme");
        }
        return $this->production;
    }

    /**
     * @return int
     * @throws Exception
     */
    private function getSymbolIndex(): int
    {
        if (!isset($this->symbolIndex)) {
            throw new Exception("No symbol index defined in symbol translation scheme");
        }
        return $this->symbolIndex;
    }
}
