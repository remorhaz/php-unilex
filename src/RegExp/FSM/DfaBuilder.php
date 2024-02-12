<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\IO\CharBufferInterface;

use function array_merge;
use function array_unique;
use function sort;

class DfaBuilder
{
    private ?NfaCalc $nfaCalc = null;

    /**
     * @var list<array{int, list<int>}>
     */
    private array $stateBuffer = [];

    public function __construct(
        private readonly Dfa $dfa,
        private readonly Nfa $nfa,
    ) {
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

        $stateQuery = [];
        $nonEquivalentStates = [];
        $dfaStates = $dfa->getStateMap()->getStateList();
        $devilState = max($dfaStates) + 1;
        $dfaStates[] = $devilState;

        $transitionMap = [];

        foreach ($dfaStates as $stateA) {
            foreach ($dfaStates as $stateB) {
                $marked = $nonEquivalentStates[$stateA][$stateB] ?? false;
                if ($marked) {
                    continue;
                }
                $isFinishStateA = $stateA != $devilState && $dfa->getStateMap()->isFinishState($stateA);
                $isFinishStateB = $stateB != $devilState && $dfa->getStateMap()->isFinishState($stateB);
                if ($isFinishStateA == $isFinishStateB) {
                    continue;
                }
                $nonEquivalentStates[$stateA][$stateB] = true;
                $nonEquivalentStates[$stateB][$stateA] = true;
                $stateQuery[] = [$stateA, $stateB];
            }
            $transitionMap[$stateA][$devilState] = $dfa->getSymbolTable()->getSymbolList();
        }

        // Making all finish states non-equivalent to distinguish regular expressions
        /*foreach ($dfa->getStateMap()->getFinishStateList() as $stateA) {
            foreach ($dfa->getStateMap()->getFinishStateList() as $stateB) {
                $marked = $nonEquivalentStates[$stateA][$stateB] ?? false;
                if ($marked) {
                    continue;
                }
                $nonEquivalentStates[$stateA][$stateB] = true;
                $nonEquivalentStates[$stateB][$stateA] = true;
                $stateQuery[] = [$stateA, $stateB];
            }
        }*/

        foreach ($dfa->getTransitionMap()->getTransitionList() as $sourceState => $transitions) {
            $devilStateSymbols = $dfa->getSymbolTable()->getSymbolList();
            foreach ($transitions as $targetState => $symbols) {
                $devilStateSymbols = array_diff($devilStateSymbols, $symbols);
                $transitionMap[$sourceState][$targetState] = $symbols;
            }
            $transitionMap[$sourceState][$devilState] = $devilStateSymbols;
        }

        while (!empty($stateQuery)) {
            [$firstTargetState, $secondTargetState] = array_pop($stateQuery);

            foreach ($dfaStates as $stateA) {
                foreach ($dfaStates as $stateB) {
                    $isMarked = $nonEquivalentStates[$stateA][$stateB] ?? false;
                    if ($isMarked) {
                        continue;
                    }
                    $symbolsA = $transitionMap[$stateA][$firstTargetState] ?? [];
                    $symbolsB = $transitionMap[$stateB][$secondTargetState] ?? [];
                    $symbols = array_intersect($symbolsA, $symbolsB);
                    if (!empty($symbols)) {
                        $nonEquivalentStates[$stateA][$stateB] = true;
                        $nonEquivalentStates[$stateB][$stateA] = true;
                        $stateQuery[] = [$stateA, $stateB];
                    }
                }
            }
        }

        $equivalentStates = [];
        $nextClass = 1;
        $classes = [];
        foreach ($dfaStates as $stateA) {
            if (!isset($classes[$stateA])) {
                $class = $nextClass++;
                foreach ($dfaStates as $stateB) {
                    $isMarked = $nonEquivalentStates[$stateA][$stateB] ?? false;
                    if (!$isMarked) {
                        $classes[$stateB] = $class;
                        if ($stateB != $devilState) {
                            $equivalentStates[$class][$stateB] = true;
                        }
                    }
                }
            }
        }

        $minimizedDfa = new Dfa();
        $minimizedDfa->setSymbolTable($dfa->getSymbolTable());
        $nfaStartStates = $nfa->getStateMap()->getStartStateList();
        $nfaFinishStates = $nfa->getStateMap()->getFinishStateList();
        $newDfaStates = [];
        foreach ($equivalentStates as $newState => $dfaStates) {
            $newValue = [];
            foreach (array_keys($dfaStates) as $dfaState) {
                $nfaStates = $dfa->getStateMap()->getStateValue($dfaState);
                $newValue = array_merge($newValue, $nfaStates);
                $newDfaStates[$dfaState] = $newState;
            }
            $newValue = array_unique($newValue);
            sort($newValue);
            $minimizedDfa->getStateMap()->createState($newValue);
            $isStartState = !empty(array_intersect($nfaStartStates, $newValue));
            if ($isStartState) {
                $minimizedDfa->getStateMap()->addStartState($newState);
            }
            $isFinishState = !empty(array_intersect($nfaFinishStates, $newValue));
            if ($isFinishState) {
                $minimizedDfa->getStateMap()->addFinishState($newState);
            }
        }
        $transitionMap = [];
        foreach ($dfa->getTransitionMap()->getTransitionList() as $sourceState => $transitions) {
            foreach ($transitions as $targetState => $symbols) {
                $newSourceState = $newDfaStates[$sourceState];
                $newTargetState = $newDfaStates[$targetState];
                $transitionMap[$newSourceState][$newTargetState] = array_unique(
                    array_merge(
                        $transitionMap[$newSourceState][$newTargetState] ?? [],
                        $symbols
                    )
                );
            }
        }
        foreach ($transitionMap as $sourceState => $transitions) {
            foreach ($transitions as $targetState => $symbols) {
                sort($symbols);
                $minimizedDfa->getTransitionMap()->addTransition($sourceState, $targetState, $symbols);
            }
        }

        return $minimizedDfa;
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
        return $this->nfaCalc ??= new NfaCalc($this->nfa);
    }

    /**
     * @throws UniLexException
     */
    private function initStateBuffer(): void
    {
        $this->dfa->setSymbolTable($this->nfa->getSymbolTable());
        $nfaStateList = [];
        foreach ($this->nfa->getStateMap()->getStartStateList() as $nfaStartStateId) {
            $nfaStateList = array_unique(
                array_merge($nfaStateList, $this->getNfaCalc()->getEpsilonClosure($nfaStartStateId))
            );
        }
        $dfaStartStateId = $this->dfa->getStateMap()->createState($nfaStateList);
        $this->dfa->getStateMap()->addStartState($dfaStartStateId);
        $this->stateBuffer = [[$dfaStartStateId, $nfaStateList]];
    }

    /**
     * @return array{int, list<int>}|null
     */
    private function getNextState(): ?array
    {
        return array_pop($this->stateBuffer);
    }

    private function addNextState(int $dfaState, int ...$nfaStateList): void
    {
        $this->stateBuffer[] = [$dfaState, $nfaStateList];
    }
}
