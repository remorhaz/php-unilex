<?php

namespace Remorhaz\UniLex\SyntaxTree;

use Remorhaz\UniLex\Exception;

class Tree
{

    private $nextNodeId = 1;

    /**
     * @var Node[]
     */
    private $nodeMap = [];

    private $rootNodeId;

    public function createNode(string $name): Node
    {
        $node = new Node($this->getNextNodeId(), $name);
        $this->nodeMap[$node->getId()] = $node;
        return $node;
    }

    /**
     * @param int $id
     * @return Node
     * @throws Exception
     */
    public function getNode(int $id): Node
    {
        if (!isset($this->nodeMap[$id])) {
            throw new Exception("Node {$id} is not defined in syntax tree");
        }
        return $this->nodeMap[$id];
    }

    /**
     * @param Node $node
     * @throws Exception
     */
    public function setRootNode(Node $node): void
    {
        if (isset($this->rootNodeId)) {
            throw new Exception("Root node of syntax tree is already set");
        }
        $this->rootNodeId = $node->getId();
    }

    /**
     * @return Node
     * @throws Exception
     */
    public function getRootNode(): Node
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
