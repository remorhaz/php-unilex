<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\AST;

use Remorhaz\UniLex\Exception;

class Tree
{
    private int $nextNodeId = 1;

    /**
     * @var array<int, Node>
     */
    private array $nodeMap = [];

    private ?int $rootNodeId = null;

    public function createNode(string $name, int $id = null): Node
    {
        $node = new Node($id ?? $this->getNextNodeId(), $name);
        $this->nodeMap[$node->getId()] = $node;

        return $node;
    }

    /**
     * @throws Exception
     */
    public function getNode(int $id): Node
    {
        return $this->nodeMap[$id] ??
            throw new Exception("Node $id is not defined in syntax tree");
    }

    /**
     * @param Node $node
     * @throws Exception
     */
    public function setRootNode(Node $node): void
    {
        $this->rootNodeId = isset($this->rootNodeId)
            ? throw new Exception("Root node of syntax tree is already set")
            : $node->getId();
    }

    /**
     * @return Node
     * @throws Exception
     */
    public function getRootNode(): Node
    {
        return $this->getNode(
            $this->rootNodeId ?? throw new Exception("Root node of syntax tree is undefined"),
        );
    }

    private function getNextNodeId(): int
    {
        return $this->nextNodeId++;
    }
}
