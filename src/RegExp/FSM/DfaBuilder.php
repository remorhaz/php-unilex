<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\IO\CharBufferInterface;

class DfaBuilder
{

    private $dfa;

    private $nfa;

    private $nfaCalc;

    private $stateBuffer = [];

    public function __construct(Dfa $dfa, Nfa $nfa)
    {
        $this->dfa = $dfa;
        $this->nfa = $nfa;
    }

    /**
     * @param Nfa $nfa
     * @return Dfa
     * @throws UniLexException
     */
    public static function fromNfa(Nfa $nfa): Dfa
    {
        $dfa = new Dfa();
        (new self($dfa, $nfa))->run();

        return $dfa;
    }

    /**
     * @param CharBufferInterface $buffer
     * @return Dfa
     * @throws UniLexException
     */
    public static function fromBuffer(CharBufferInterface $buffer): Dfa
    {
        $nfa = NfaBuilder::fromBuffer($buffer);

        return self::fromNfa($nfa);
    }

    /**
     * @param Tree $tree
     * @return Dfa
     * @throws UniLexException
     */
    public static function fromTree(Tree $tree): Dfa
    {
        $nfa = NfaBuilder::fromTree($tree);

        return self::fromNfa($nfa);
    }

    /**
     * @throws UniLexException
     */
    public function run(): void
    {
        $this->initStateBuffer();
        while ($state = $this->getNextState()) {
            [$dfaStateIn, $nfaStateInList] = $state;
            foreach ($this->getMovesBySymbol(...$nfaStateInList) as $symbolId => $nfaStateOutList) {
                $dfaStateOut = $this->createStateIfNotExists($isStateProcessed, ...$nfaStateOutList);
                $this->mergeTransition($dfaStateIn, $dfaStateOut, $symbolId);
                if (!$isStateProcessed) {
                    $this->addNextState($dfaStateOut, ...$nfaStateOutList);
                }
            }
        }
    }

    private function getMovesBySymbol(int ...$nfaStateList): array
    {
        $result = [];
        foreach ($this->dfa->getSymbolTable()->getSymbolList() as $symbolId) {
            $symbolMoves = $this->getNfaCalc()->getSymbolMoves($symbolId, ...$nfaStateList);
            $nextStateList = $this->getNfaCalc()->getEpsilonClosure(...$symbolMoves);
            if (!empty($nextStateList)) {
                $result[$symbolId] = $nextStateList;
            }
        }

        return $result;
    }

    /**
     * @param bool $exists
     * @param int  ...$nfaStateList
     * @return int
     * @throws UniLexException
     */
    private function createStateIfNotExists(&$exists, int ...$nfaStateList): int
    {
        $dfaStateMap = $this->dfa->getStateMap();
        $exists = $dfaStateMap->stateValueExists($nfaStateList);
        if ($exists) {
            return $dfaStateMap->getValueState($nfaStateList);
        }
        $dfaState = $dfaStateMap->createState($nfaStateList);
        foreach ($nfaStateList as $nfaState) {
            if ($this->nfa->getStateMap()->isFinishState($nfaState)) {
                $dfaStateMap->addFinishState($dfaState);
                break;
            }
        }

        return $dfaState;
    }

    /**
     * @param int $stateIn
     * @param int $stateOut
     * @param int $symbolId
     * @throws UniLexException
     */
    private function mergeTransition(int $stateIn, int $stateOut, int $symbolId): void
    {
        $transitionMap = $this
            ->dfa
            ->getTransitionMap();
        $symbolList = $transitionMap->transitionExists($stateIn, $stateOut)
            ? $transitionMap->getTransition($stateIn, $stateOut)
            : [];
        $symbolList[] = $symbolId;
        $transitionMap->replaceTransition($stateIn, $stateOut, $symbolList);
    }

    private function getNfaCalc(): NfaCalc
    {
        if (!isset($this->nfaCalc)) {
            $this->nfaCalc = new NfaCalc($this->nfa);
        }

        return $this->nfaCalc;
    }

    /**
     * @throws UniLexException
     */
    private function initStateBuffer(): void
    {
        $this->dfa->setSymbolTable($this->nfa->getSymbolTable());
        $startStateId = $this->nfa->getStateMap()->getStartState();
        $nfaStateList = $this->getNfaCalc()->getEpsilonClosure($startStateId);
        $startState = $this->dfa->getStateMap()->createState($nfaStateList);
        $this->dfa->getStateMap()->setStartState($startState);
        $this->stateBuffer = [[$startState, $nfaStateList]];
    }

    private function getNextState(): ?array
    {
        return array_pop($this->stateBuffer);
    }

    private function addNextState(int $dfaState, int ...$nfaStateList): void
    {
        $this->stateBuffer[] = [$dfaState, $nfaStateList];
    }
}
