<?php

namespace Remorhaz\UniLex\RegExp\AST;

use Remorhaz\UniLex\AST\AbstractTranslatorListener;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Stack\PushInterface;

class FsaBuilder extends AbstractTranslatorListener
{

    /**
     * @param Node $node
     * @param PushInterface $stack
     * @throws Exception
     */
    public function onBeginProduction(Node $node, PushInterface $stack): void
    {
        switch ($node->getName()) {
            case NodeType::SYMBOL:
                if (!empty($node->getChildList())) {
                    throw new Exception("AST node '{$node->getName()}' should not have child nodes");
                }
                break;

            default:
                throw new Exception("Unknown AST node name: {$node->getName()}");
        }
    }
}
