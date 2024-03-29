<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\AST;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\AbstractTranslatorListener;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\AST\Symbol;
use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Stack\PushInterface;

#[CoversClass(Translator::class)]
class TranslatorTest extends TestCase
{
    /**
     * @throws UniLexException
     */
    public function testRun_ValidTree_TriggersOnFinishProductionWithRootNodeOnce(): void
    {
        $tree = new Tree();
        $tree->setRootNode($tree->createNode('a'));
        $listener = $this
            ->createMock(AbstractTranslatorListener::class);
        $listener
            ->expects($this->once())
            ->method('onFinishProduction')
            ->with($tree->getRootNode());
        /** @var AbstractTranslatorListener $listener */
        (new Translator($tree, $listener))->run();
    }

    /**
     * @throws UniLexException
     */
    public function testRun_ValidTree_TriggersOnBeginProductionWithRootNodeOnce(): void
    {
        $tree = new Tree();
        $tree->setRootNode($tree->createNode('a'));
        $listener = $this
            ->createMock(AbstractTranslatorListener::class);
        $listener
            ->expects($this->once())
            ->method('onBeginProduction')
            ->with($tree->getRootNode(), $this->anything());
        /** @var AbstractTranslatorListener $listener */
        (new Translator($tree, $listener))->run();
    }

    /**
     * @throws UniLexException
     */
    public function testRun_ListenerPushesRootChild_TriggersOnSymbolWithNodeOnce(): void
    {
        $tree = new Tree();
        $tree->setRootNode($tree->createNode('a'));
        $childNode = $tree->createNode('b');
        $expectedSymbol = new Symbol($childNode, 0);
        $tree->getRootNode()->addChild($childNode);
        $listener = $this
            ->createMock(AbstractTranslatorListener::class);
        $onBeginProduction = function (Node $node, PushInterface $stack) use ($expectedSymbol): void {
            if ($node->getName() == 'a') {
                $stack->push($expectedSymbol);
            }
        };
        $listener
            ->expects($this->any())
            ->method('onBeginProduction')
            ->willReturnCallback($onBeginProduction);
        $listener
            ->expects($this->once())
            ->method('onSymbol')
            ->with($expectedSymbol);
        /** @var AbstractTranslatorListener $listener */
        (new Translator($tree, $listener))->run();
    }
}
