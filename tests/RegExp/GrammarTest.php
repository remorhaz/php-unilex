<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Parser\SyntaxTree\Node;
use Remorhaz\UniLex\Parser\SyntaxTree\Tree;
use Remorhaz\UniLex\RegExp\ParserFactory;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

/**
 * @covers \Remorhaz\UniLex\RegExp\Grammar\TranslationSchemeConfig
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
        ParserFactory::createFromBuffer($tree, $buffer)->run();
        self::assertEquals($expectedValue, $this->exportSyntaxTree($tree));
    }

    public function providerSyntaxTree(): array
    {
        $symbolEmpty = (object) [
            'name' => 'empty',
        ];
        $symbolA = (object) [
            'name' => 'symbol',
            'attr' => (object) ['code' => 0x61],
        ];
        $symbolB = (object) [
            'name' => 'symbol',
            'attr' => (object) ['code' => 0x62],
        ];
        $symbolC = (object) [
            'name' => 'symbol',
            'attr' => (object) ['code' => 0x63],
        ];
        return [
            "Empty string" => ['', $symbolEmpty],
            "Single symbol (skips concatenate node)" => ['a', $symbolA],
            "Concatenation of two symbols" => [
                'ab',
                (object) [
                    'name' => 'concatenate',
                    'nodes' => [$symbolA, $symbolB],
                ],
            ],
            "Concatenation of three symbols" => [
                'abc',
                (object) [
                    'name' => 'concatenate',
                    'nodes' => [$symbolA, $symbolB, $symbolC],
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
            "Open long number range of symbols (limit)" => [
                'a{13,}',
                (object) [
                    'name' => 'repeat',
                    'attr' => (object) ['min' => 13, 'max' => 0, 'is_max_infinite' => true],
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
            "Alternative of two symbols" => [
                'a|b',
                (object) [
                    'name' => 'alternative',
                    'nodes' => [$symbolA, $symbolB],
                ],
            ],
            "Alternative of three symbols" => [
                'a|b|c',
                (object) [
                    'name' => 'alternative',
                    'nodes' => [$symbolA, $symbolB, $symbolC],
                ],
            ],
            "Alternative of two symbols and empty string between them" => [
                'a||b',
                (object) [
                    'name' => 'alternative',
                    'nodes' => [$symbolA, $symbolEmpty, $symbolB],
                ],
            ],
            "Any symbol (.)" => [
                '.',
                (object) [
                    'name' => 'symbol_any',
                ],
            ],
            "Symbol in a group" => ['(a)', $symbolA],
            "Concatenation of two symbols inside and outside of a group" => [
                'a(b)',
                (object) [
                    'name' => 'concatenate',
                    'nodes' => [$symbolA, $symbolB],
                ]
            ],
            "Repeated alternative of two symbols in a group" => [
                '(a|b)+',
                (object) [
                    'name' => 'repeat',
                    'attr' => (object) ['min' => 1, 'max' => 0, 'is_max_infinite' => true],
                    'nodes' => [
                        (object) [
                            'name' => 'alternative',
                            'nodes' => [$symbolA, $symbolB],
                        ],
                    ],
                ],
            ],
            "Single line start assert" => [
                '^',
                (object) [
                    'name' => 'assert',
                    'attr' => (object) ['type' => 'line_start'],
                ],
            ],
            "Concatenation of symbol and assert" => [
                'a$',
                (object) [
                    'name' => 'concatenate',
                    'nodes' => [
                        $symbolA,
                        (object) [
                            'name' => 'assert',
                            'attr' => (object) ['type' => 'line_finish'],
                        ],
                    ],
                ],
             ],
            "Simple escaped symbol" => [
                '\\s',
                (object) [
                    'name' => 'esc_simple',
                    'attr' => (object) ['code' => 0x73],
                ],
            ],
            "Special escaped symbol" => [
                '\\$',
                (object) [
                    'name' => 'symbol',
                    'attr' => (object) ['code' => 0x24]
                ],
            ],
            "Symbol with Unicode property (full)" => [
                '\\p{Greek}',
                (object) [
                    'name' => 'symbol_prop',
                    'attr' => (object) ['not' => false, 'name' => [0x47, 0x72, 0x65, 0x65, 0x6B]]
                ],
            ],
            "Symbol without Unicode property (full)" => [
                '\\P{Greek}',
                (object) [
                    'name' => 'symbol_prop',
                    'attr' => (object) ['not' => true, 'name' => [0x47, 0x72, 0x65, 0x65, 0x6B]]
                ],
            ],
            "Symbol with Unicode property (short)" => [
                '\\pL',
                (object) [
                    'name' => 'symbol_prop',
                    'attr' => (object) ['not' => false, 'name' => [0x4C]]
                ],
            ],
            "Symbol without Unicode property (short)" => [
                '\\PL',
                (object) [
                    'name' => 'symbol_prop',
                    'attr' => (object) ['not' => true, 'name' => [0x4C]]
                ],
            ],
            "Escaped Unicode symbol" => ['\\u0061', $symbolA],
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
