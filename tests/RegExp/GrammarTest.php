<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Parser\SyntaxTree\Node;
use Remorhaz\UniLex\Parser\SyntaxTree\Tree;
use Remorhaz\UniLex\RegExp\AST\NodeType;
use Remorhaz\UniLex\RegExp\ParserFactory;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

/**
 * @covers \Remorhaz\UniLex\RegExp\Grammar\TranslationScheme
 * @covers \Remorhaz\UniLex\RegExp\Grammar\SymbolTranslationScheme
 * @covers \Remorhaz\UniLex\RegExp\Grammar\ProductionTranslationScheme
 * @covers \Remorhaz\UniLex\RegExp\Grammar\TokenTranslationScheme
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
            'name' => NodeType::EMPTY,
        ];
        $symbolA = (object) [
            'name' => NodeType::SYMBOL,
            'attr' => (object) ['code' => 0x61],
        ];
        $symbolB = (object) [
            'name' => NodeType::SYMBOL,
            'attr' => (object) ['code' => 0x62],
        ];
        $symbolC = (object) [
            'name' => NodeType::SYMBOL,
            'attr' => (object) ['code' => 0x63],
        ];
        return [
            "Empty string" => ['', $symbolEmpty],
            "Single symbol (skips concatenate node)" => ['a', $symbolA],
            "Concatenation of two symbols" => [
                'ab',
                (object) [
                    'name' => NodeType::CONCATENATE,
                    'nodes' => [$symbolA, $symbolB],
                ],
            ],
            "Concatenation of three symbols" => [
                'abc',
                (object) [
                    'name' => NodeType::CONCATENATE,
                    'nodes' => [$symbolA, $symbolB, $symbolC],
                ],
            ],
            "Optional symbol (?)" => [
                'a?',
                (object) [
                    'name' => NodeType::REPEAT,
                    'attr' => (object) ['min' => 0, 'max' => 1, 'is_max_infinite' => false],
                    'nodes' => [$symbolA],
                ],
            ],
            "Zero or many symbols (*)" => [
                'a*',
                (object) [
                    'name' => NodeType::REPEAT,
                    'attr' => (object) ['min' => 0, 'max' => 0, 'is_max_infinite' => true],
                    'nodes' => [$symbolA],
                ],
            ],
            "One or many symbols (+)" => [
                'a+',
                (object) [
                    'name' => NodeType::REPEAT,
                    'attr' => (object) ['min' => 1, 'max' => 0, 'is_max_infinite' => true],
                    'nodes' => [$symbolA],
                ],
            ],
            "Exact number of symbols (limit)" => [
                'a{5}',
                (object) [
                    'name' => NodeType::REPEAT,
                    'attr' => (object) ['min' => 5, 'max' => 5, 'is_max_infinite' => false],
                    'nodes' => [$symbolA],
                ],
            ],
            "Open long number range of symbols (limit)" => [
                'a{13,}',
                (object) [
                    'name' => NodeType::REPEAT,
                    'attr' => (object) ['min' => 13, 'max' => 0, 'is_max_infinite' => true],
                    'nodes' => [$symbolA],
                ],
            ],
            "Fixed number range of symbols (limit)" => [
                'a{3,5}',
                (object) [
                    'name' => NodeType::REPEAT,
                    'attr' => (object) ['min' => 3, 'max' => 5, 'is_max_infinite' => false],
                    'nodes' => [$symbolA],
                ],
            ],
            "Exactly one symbol (limit) (skips repeat node)" => ['a{1,1}', $symbolA],
            "Alternative of two symbols" => [
                'a|b',
                (object) [
                    'name' => NodeType::ALTERNATIVE,
                    'nodes' => [$symbolA, $symbolB],
                ],
            ],
            "Alternative of three symbols" => [
                'a|b|c',
                (object) [
                    'name' => NodeType::ALTERNATIVE,
                    'nodes' => [$symbolA, $symbolB, $symbolC],
                ],
            ],
            "Alternative of two symbols and empty string between them" => [
                'a||b',
                (object) [
                    'name' => NodeType::ALTERNATIVE,
                    'nodes' => [$symbolA, $symbolEmpty, $symbolB],
                ],
            ],
            "Any symbol (.)" => [
                '.',
                (object) [
                    'name' => NodeType::SYMBOL_ANY,
                ],
            ],
            "Symbol in a group" => ['(a)', $symbolA],
            "Concatenation of two symbols inside and outside of a group" => [
                'a(b)',
                (object) [
                    'name' => NodeType::CONCATENATE,
                    'nodes' => [$symbolA, $symbolB],
                ]
            ],
            "Repeated alternative of two symbols in a group" => [
                '(a|b)+',
                (object) [
                    'name' => NodeType::REPEAT,
                    'attr' => (object) ['min' => 1, 'max' => 0, 'is_max_infinite' => true],
                    'nodes' => [
                        (object) [
                            'name' => NodeType::ALTERNATIVE,
                            'nodes' => [$symbolA, $symbolB],
                        ],
                    ],
                ],
            ],
            "Single line start assert" => [
                '^',
                (object) [
                    'name' => NodeType::ASSERT,
                    'attr' => (object) ['type' => 'line_start'],
                ],
            ],
            "Concatenation of symbol and assert" => [
                'a$',
                (object) [
                    'name' => NodeType::CONCATENATE,
                    'nodes' => [
                        $symbolA,
                        (object) [
                            'name' => NodeType::ASSERT,
                            'attr' => (object) ['type' => 'line_finish'],
                        ],
                    ],
                ],
             ],
            "Simple escaped symbol" => [
                '\\s',
                (object) [
                    'name' => NodeType::ESC_SIMPLE,
                    'attr' => (object) ['code' => 0x73],
                ],
            ],
            "Special escaped symbol" => [
                '\\$',
                (object) [
                    'name' => NodeType::SYMBOL,
                    'attr' => (object) ['code' => 0x24]
                ],
            ],
            "Symbol with Unicode property (full)" => [
                '\\p{Greek}',
                (object) [
                    'name' => NodeType::SYMBOL_PROP,
                    'attr' => (object) ['not' => false, 'name' => [0x47, 0x72, 0x65, 0x65, 0x6B]]
                ],
            ],
            "Symbol without Unicode property (full)" => [
                '\\P{Greek}',
                (object) [
                    'name' => NodeType::SYMBOL_PROP,
                    'attr' => (object) ['not' => true, 'name' => [0x47, 0x72, 0x65, 0x65, 0x6B]]
                ],
            ],
            "Symbol with Unicode property (short)" => [
                '\\pL',
                (object) [
                    'name' => NodeType::SYMBOL_PROP,
                    'attr' => (object) ['not' => false, 'name' => [0x4C]]
                ],
            ],
            "Symbol without Unicode property (short)" => [
                '\\PL',
                (object) [
                    'name' => NodeType::SYMBOL_PROP,
                    'attr' => (object) ['not' => true, 'name' => [0x4C]]
                ],
            ],
            "Escaped Unicode symbol" => ['\\u0061', $symbolA],
            "Escaped short hexadecimal symbol" => ['\\x61', $symbolA],
            "Escaped long hexadecimal symbol" => ['\\x{061}', $symbolA],
            "Escaped control symbol" => [
                '\\c?',
                (object) [
                    'name' => NodeType::SYMBOL_CTL,
                    'attr' => (object) ['code' => 0x3F],
                ],
            ],
            "Escaped long octal symbol" => ['\\o{141}', $symbolA],
            "Escaped short octal symbol (single zero)" => [
                '\\0',
                (object) [
                    'name' => NodeType::SYMBOL,
                    'attr' => (object) ['code' => 0x00],
                ],
            ],
            "Single symbol in class" => [
                '[a]',
                (object) [
                    'name' => NodeType::SYMBOL,
                    'attr' => (object) ['code' => 0x61],
                ],
            ],
            "Single symbol in negative class" => [
                '[^a]',
                (object) [
                    'name' => NodeType::SYMBOL_CLASS,
                    'attr' => (object) ['not' => true],
                    'nodes' => [
                        (object) [
                            'name' => NodeType::SYMBOL,
                            'attr' => (object) ['code' => 0x61],
                        ],
                    ],
                ]
            ],
            "Two symbols in class" => [
                '[ab]',
                (object) [
                    'name' => NodeType::SYMBOL_CLASS,
                    'attr' => (object) ['not' => false],
                    'nodes' => [$symbolA, $symbolB],
                ],
            ],
            "Circumflex in inverted class" => [
                '[^^]',
                (object) [
                    'name' => NodeType::SYMBOL_CLASS,
                    'attr' => (object) ['not' => true],
                    'nodes' => [
                        (object) [
                            'name' => NodeType::SYMBOL,
                            'attr' => (object) ['code' => 0x5E],
                        ],
                    ],
                ],
            ],
            "Right square bracket in class" => [
                '[]]',
                (object) [
                    'name' => NodeType::SYMBOL,
                    'attr' => (object) ['code' => 0x5D],
                ],
            ],
            "Right square bracket in inverted class" => [
                '[^]]',
                (object) [
                    'name' => NodeType::SYMBOL_CLASS,
                    'attr' => (object) ['not' => true],
                    'nodes' => [
                        (object) [
                            'name' => NodeType::SYMBOL,
                            'attr' => (object) ['code' => 0x5D],
                        ],
                    ],
                ],
            ],
            "One range in class" => [
                '[a-c]',
                (object) [
                    'name' => NodeType::SYMBOL_RANGE,
                    'nodes' => [$symbolA, $symbolC],
                ],
            ],
            "One range in inverted class" => [
                '[^a-c]',
                (object) [
                    'name' => NodeType::SYMBOL_CLASS,
                    'attr' => (object) ['not' => true],
                    'nodes' => [
                        (object) [
                            'name' => NodeType::SYMBOL_RANGE,
                            'nodes' => [$symbolA, $symbolC],
                        ],
                    ],
                ],
            ],
            "Two ranges and a symbol between in class" => [
                '[a-cbb-c]',
                (object) [
                    'name' => NodeType::SYMBOL_CLASS,
                    'attr' => (object) ['not' => false],
                    'nodes' => [
                        (object) [
                            'name' => NodeType::SYMBOL_RANGE,
                            'nodes' => [$symbolA, $symbolC],
                        ],
                        $symbolB,
                        (object) [
                            'name' => NodeType::SYMBOL_RANGE,
                            'nodes' => [$symbolB, $symbolC],
                        ],
                    ],
                ],
            ],
            "Escaped symbol in first position of class" => [
                '[\\s]',
                (object) [
                    'name' => NodeType::ESC_SIMPLE,
                    'attr' => (object) ['code' => 0x73],
                ],
            ],
            "Escaped symbol in first position of inverted class" => [
                '[^\\s]',
                (object) [
                    'name' => NodeType::SYMBOL_CLASS,
                    'attr' => (object) ['not' => true],
                    'nodes' => [
                        (object) [
                            'name' => NodeType::ESC_SIMPLE,
                            'attr' => (object) ['code' => 0x73],
                        ],
                    ],
                ],
            ],
            "Escaped symbol in second position of class" => [
                '[a\\s]',
                (object) [
                    'name' => NodeType::SYMBOL_CLASS,
                    'attr' => (object) ['not' => false],
                    'nodes' => [
                        $symbolA,
                        (object) [
                            'name' => NodeType::ESC_SIMPLE,
                            'attr' => (object) ['code' => 0x73],
                        ],
                    ],
                ],
            ],
            "Escaped symbol in second position of inverted class" => [
                '[^a\\s]',
                (object) [
                    'name' => NodeType::SYMBOL_CLASS,
                    'attr' => (object) ['not' => true],
                    'nodes' => [
                        $symbolA,
                        (object) [
                            'name' => NodeType::ESC_SIMPLE,
                            'attr' => (object) ['code' => 0x73],
                        ],
                    ],
                ],
            ],
            "Range between escaped symbols in class" => [
                '[\\t-\\-]',
                (object) [
                    'name' => NodeType::SYMBOL_RANGE,
                    'nodes' => [
                        (object) [
                            'name' => NodeType::ESC_SIMPLE,
                            'attr' => (object) ['code' => 0x74],
                        ],
                        (object) [
                            'name' => NodeType::SYMBOL,
                            'attr' => (object) ['code' => 0x2D],
                        ],
                    ],
                ],
            ],
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
