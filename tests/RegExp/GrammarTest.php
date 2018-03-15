<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Parser\SyntaxTree\Node;
use Remorhaz\UniLex\Parser\SyntaxTree\Tree;
use Remorhaz\UniLex\RegExp\ParserFactory;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

/**
 * @coversNothing
 */
class GrammarTest extends TestCase
{

    /**
     * @param string $text
     * @param $expectedValue
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerSyntaxTree
     * @covers \Remorhaz\UniLex\Parser\SyntaxTree\Node
     */
    public function testRun_ValidBuffer_CreatesMatchingSyntaxTree(string $text, $expectedValue): void
    {
        $buffer = CharBufferFactory::createFromUtf8String($text);
        $tree = new Tree;
        $parser = ParserFactory::createFromBuffer($tree, $buffer);
        $parser->run();
        self::assertEquals($expectedValue, $this->exportSyntaxTree($tree));
    }

    public function providerSyntaxTree(): array
    {
        $symbolA = (object) [
            'name' => 'symbol',
            'attr' => (object) ['code' => 0x61],
        ];
        $symbolB = (object) [
            'name' => 'symbol',
            'attr' => (object) ['code' => 0x62],
        ];
        return [
            "Single symbol (skips concatenate node)" => ['a', $symbolA],
            "Concatenation of two latin chars" => [
                'ab',
                (object) [
                    'name' => 'concatenate',
                    'nodes' => [$symbolA, $symbolB],
                ],
            ],
            "Optional symbol (?)" => [
                'a?',
                (object) [
                    'name' => 'repeat',
                    'attr' => (object) ['min' => 0, 'max' => 1, 'is_max_infinite' => false],
                    'nodes' => [$symbolA],
                ],
            ],
            "Zero or many symbols (*)" => [
                'a*',
                (object) [
                    'name' => 'repeat',
                    'attr' => (object) ['min' => 0, 'max' => 0, 'is_max_infinite' => true],
                    'nodes' => [$symbolA],
                ],
            ],
            "One or many symbols (+)" => [
                'a+',
                (object) [
                    'name' => 'repeat',
                    'attr' => (object) ['min' => 1, 'max' => 0, 'is_max_infinite' => true],
                    'nodes' => [$symbolA],
                ],
            ],
            "Exact number of symbols (limit)" => [
                'a{5}',
                (object) [
                    'name' => 'repeat',
                    'attr' => (object) ['min' => 5, 'max' => 5, 'is_max_infinite' => false],
                    'nodes' => [$symbolA],
                ],
            ],
            "Open number range of symbols (limit)" => [
                'a{3,}',
                (object) [
                    'name' => 'repeat',
                    'attr' => (object) ['min' => 3, 'max' => 0, 'is_max_infinite' => true],
                    'nodes' => [$symbolA],
                ],
            ],
            "Fixed number range of symbols (limit)" => [
                'a{3,5}',
                (object) [
                    'name' => 'repeat',
                    'attr' => (object) ['min' => 3, 'max' => 5, 'is_max_infinite' => false],
                    'nodes' => [$symbolA],
                ],
            ],
            "Exactly one symbol (limit) (skips repeat node)" => ['a{1,1}', $symbolA],
        ];
    }

    /**
     * @param Tree $tree
     * @return mixed
     * @throws \Remorhaz\UniLex\Exception
     */
    private function exportSyntaxTree(Tree $tree)
    {
        return $this->exportSyntaxTreeNode($tree->getRootNode());
    }

    /**
     * @param Node $node
     * @return mixed
     */
    private function exportSyntaxTreeNode(Node $node)
    {
        $data = [
            'name' => $node->getName(),
        ];
        $attributeList = $node->getAttributeList();
        if (!empty($attributeList)) {
            $data['attr'] = (object) $attributeList;
        }
        $childList = [];
        foreach ($node->getChildList() as $childNode) {
            $childList[] = $this->exportSyntaxTreeNode($childNode);
        }
        if (!empty($childList)) {
            $data['nodes'] = $childList;
        }
        return (object) $data;
    }
}
