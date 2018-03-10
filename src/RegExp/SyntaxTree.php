<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\Exception;

class SyntaxTree
{

    private $nextNodeId = 1;

    /**
     * @var SyntaxTreeNode[]
     */
    private $nodeMap = [];

    private $rootNodeId;

    public function createNode(string $name): SyntaxTreeNode
    {
        $node = new SyntaxTreeNode($this->getNextNodeId(), $name);
        $this->nodeMap[$node->getId()] = $node;
        return $node;
    }

    /**
     * @param int $id
     * @return SyntaxTreeNode
     * @throws Exception
     */
    public function getNode(int $id): SyntaxTreeNode
    {
        if (!isset($this->nodeMap[$id])) {
            throw new Exception("Node {$id} is not defined in syntax tree");
        }
        return $this->nodeMap[$id];
    }

    /**
     * @param SyntaxTreeNode $node
     * @throws Exception
     */
    public function setRootNode(SyntaxTreeNode $node): void
    {
        if (isset($this->rootNodeId)) {
            throw new Exception("Root node of syntax tree is already set");
        }
        $this->rootNodeId = $node->getId();
    }

    /**
     * @return SyntaxTreeNode
     * @throws Exception
     */
    public function getRootNode(): SyntaxTreeNode
    {
        if (!isset($this->rootNodeId)) {
            throw new Exception("Root node of syntax tree is undefined");
        }
        return $this->getNode($this->rootNodeId);
    }

    private function getNextNodeId(): int
    {
        return $this->nextNodeId++;
    }
}
