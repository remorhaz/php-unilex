<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

use ReflectionException;
use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\RegExp\FSM\Dfa;
use Remorhaz\UniLex\RegExp\FSM\DfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\Nfa;
use Remorhaz\UniLex\RegExp\FSM\NfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;
use Remorhaz\UniLex\RegExp\FSM\TransitionMap;
use Remorhaz\UniLex\RegExp\ParserFactory;
use Remorhaz\UniLex\RegExp\PropertyLoader;
use Remorhaz\UniLex\Unicode\CharBufferFactory;
use Throwable;

use function array_diff;
use function array_intersect;
use function array_keys;
use function array_merge;
use function array_pop;
use function array_unique;
use function count;
use function implode;
use function in_array;
use function var_export;

class TokenMatcherGenerator
{

    private $spec;

    private $output;

    private $dfa;

    private $regExpMap = [];

    private $visitedHashMap = [];

    public function __construct(TokenMatcherSpec $spec)
    {
        $this->spec = $spec;
    }

    /**
     * @return string
     * @throws Exception
     * @throws ReflectionException
     */
    private function buildOutput(): string
    {
        return
            "{$this->buildFileComment()}\ndeclare(strict_types=1);\n\n" .
            "{$this->buildHeader()}\n" .
            "class {$this->spec->getTargetShortName()} extends {$this->spec->getTemplateClass()->getShortName()}\n" .
            "{\n" .
            "\n" .
            "    public function match({$this->buildMatchParameters()}): bool\n" .
            "    {\n{$this->buildMatchBody()}" .
            "    }\n" .
            "}\n";
    }

    /**
     * @return TokenMatcherInterface
     * @throws Exception
     */
    public function load(): TokenMatcherInterface
    {
        $targetClass = $this->spec->getTargetClassName();
        if (!class_exists($targetClass)) {
            try {
                $source = $this->getOutput(false);
                eval($source);
            } catch (Throwable $e) {
                throw new Exception("Invalid PHP code generated", 0, $e);
            }
            if (!class_exists($targetClass)) {
                throw new Exception("Failed to generate target class");
            }
        }

        return new $targetClass();
    }

    /**
     * @param bool $asFile
     * @return string
     * @throws Exception
     * @throws ReflectionException
     */
    public function getOutput(bool $asFile = true): string
    {
        if (!isset($this->output)) {
            $this->output = $this->buildOutput();
        }

        return $asFile ? "<?php\n\n{$this->output}" : $this->output;
    }

    private function buildFileComment(): string
    {
        $content = $this->spec->getFileComment();
        if ('' == $content) {
            return '';
        }
        $comment = "/**\n";
        $commentLineList = explode("\n", $content);
        foreach ($commentLineList as $commentLine) {
            $comment .= rtrim(" * {$commentLine}") . "\n";
        }
        $comment .= " */\n";

        return $comment;
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public function buildHeader(): string
    {
        $headerParts = [];
        $namespace = $this->spec->getTargetNamespaceName();
        if ($namespace != '') {
            $headerParts[] = $this->buildMethodPart("namespace {$namespace};", 0);
        }
        $useList = $this->buildUseList();
        if ('' != $useList) {
            $headerParts[] = $useList;
        }
        $header = $this->buildMethodPart($this->spec->getHeader(), 0);
        if ('' != $header) {
            $headerParts[] = $header;
        }

        return implode("\n", $headerParts);
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    private function buildUseList(): string
    {
        $result = '';
        foreach ($this->spec->getUsedClassList() as $alias => $className) {
            $classWithAlias = is_string($alias) ? "{$className} {$alias}" : $className;
            $result .= $this->buildMethodPart("use {$classWithAlias};", 0);
        }

        return $result;
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    private function buildMatchParameters(): string
    {
        $paramList = [];
        foreach ($this->spec->getMatchMethod()->getParameters() as $matchParameter) {
            if ($matchParameter->hasType()) {
                $param = $matchParameter->getType()->isBuiltin()
                    ? $matchParameter->getType()->getName()
                    : $matchParameter->getClass()->getShortName();
                $param .= " ";
            } else {
                $param = "";
            }
            $param .= "\${$matchParameter->getName()}";
            $paramList[] = $param;
        }

        return implode(", ", $paramList);
    }

    /**
     * @return string
     * @throws Exception
     */
    private function buildMatchBody(): string
    {
        $result = $this->buildBeforeMatch();

        foreach ($this->spec->getModeList() as $mode) {
            if (TokenMatcherInterface::DEFAULT_MODE == $mode) {
                continue;
            }
            $result .=
                $this->buildMethodPart("if (\$context->getMode() == '{$mode}') {") .
                $this->buildFsmEntry($mode, 3) .
                $this->buildMethodPart("}");
        }
        foreach ($this->spec->getModeList() as $mode) {
            if (TokenMatcherInterface::DEFAULT_MODE == $mode) {
                $result .= $this->buildFsmEntry(TokenMatcherInterface::DEFAULT_MODE) . "\n";
            }
            $result .= $this->buildFsmMoves($mode);
        }

        $result .= $this->buildErrorState();

        return $result;
    }

    private function buildBeforeMatch(): string
    {
        return
            $this->buildMethodPart("\$context = \$this->createContext(\$buffer, \$tokenFactory);") .
            $this->buildMethodPart($this->spec->getBeforeMatch());
    }

    /**
     * @param string $mode
     * @param int    $indent
     * @return string
     * @throws Exception
     */
    private function buildFsmEntry(string $mode, int $indent = 2): string
    {
        $state = $this->getDfa($mode)->getStateMap()->getStartState();

        return $this->buildMethodPart("goto {$this->buildStateLabel('state', $mode, $state)};", $indent);
    }

    private function buildStateLabel(string $prefix, string $mode, int $state): string
    {
        $contextSuffix = TokenMatcherInterface::DEFAULT_MODE == $mode
            ? ''
            : ucfirst($mode);

        return "{$prefix}{$contextSuffix}{$state}";
    }

    /**
     * @param string $mode
     * @return string
     * @throws Exception
     */
    private function buildFsmMoves(string $mode): string
    {
        $result = '';
        foreach ($this->getDfa($mode)->getStateMap()->getStateList() as $stateIn) {
            if ($this->isFinishStateWithSingleEnteringTransition($mode, $stateIn)) {
                continue;
            }
            $result .=
                $this->buildStateEntry($mode, $stateIn) .
                $this->buildStateTransitionList($mode, $stateIn) .
                $this->buildStateFinish($mode, $stateIn);
        }

        return $result;
    }

    /**
     * @param string $mode
     * @param int    $stateIn
     * @return string
     * @throws Exception
     */
    private function buildStateEntry(string $mode, int $stateIn): string
    {
        $result = '';
        $result .= $this->buildMethodPart("{$this->buildStateLabel('state', $mode, $stateIn)}:");
        $moves = $this->getDfa($mode)->getTransitionMap()->getExitList($stateIn);
        if (empty($moves)) {
            return $result;
        }
        $result .= $this->buildMethodPart("if (\$context->getBuffer()->isEnd()) {");
        $result .= $this->getDfa($mode)->getStateMap()->isFinishState($stateIn)
            ? $this->buildMethodPart("goto {$this->buildStateLabel('finish', $mode, $stateIn)};", 3)
            : $this->buildMethodPart("goto error;", 3);
        $result .=
            $this->buildMethodPart("}") .
            $this->buildMethodPart("\$char = \$context->getBuffer()->getSymbol();");

        return $result;
    }

    /**
     * @param string $mode
     * @param int    $stateIn
     * @return string
     * @throws Exception
     */
    private function buildStateTransitionList(string $mode, int $stateIn): string
    {
        $result = '';
        foreach ($this->getDfa($mode)->getTransitionMap()->getExitList($stateIn) as $stateOut => $symbolList) {
            foreach ($symbolList as $symbol) {
                $rangeSet = $this->getDfa($mode)->getSymbolTable()->getRangeSet($symbol);
                $result .=
                    $this->buildMethodPart("if ({$this->buildRangeSetCondition($rangeSet)}) {") .
                    $this->buildOnTransition() .
                    $this->buildMethodPart("\$context->getBuffer()->nextSymbol();", 3) .
                    $this->buildMarkTransitionVisited($mode, $stateIn, $stateOut, $symbol, 3);
                $result .= $this->isFinishStateWithSingleEnteringTransition($mode, $stateOut)
                    ? $this->buildToken($mode, $stateOut, 3)
                    : $this->buildStateTransition($mode, $stateOut, 3);
                $result .= $this->buildMethodPart("}");
            }
        }

        return $result;
    }

    private function buildMarkTransitionVisited(
        string $mode,
        int $stateIn,
        int $stateOut,
        int $symbol,
        int $indent = 3
    ): string {
        $result = '';
        $hash = $this->buildHash($stateIn, $stateOut, $symbol);
        foreach ($this->visitedHashMap[$mode] ?? [] as $regExps) {
            foreach ($regExps as $hashes) {
                if (in_array($hash, $hashes)) {
                    $hashArg = var_export($hash, true);
                    $result .= $this->buildMethodPart("\$context->visitTransition({$hashArg});", $indent);
                    break 2;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $mode
     * @param int    $stateOut
     * @param int    $indent
     * @return string
     */
    private function buildStateTransition(string $mode, int $stateOut, int $indent = 3): string
    {
        return $this->buildMethodPart("goto {$this->buildStateLabel('state', $mode, $stateOut)};", $indent);
    }

    /**
     * @param string $mode
     * @param int    $stateOut
     * @return bool
     * @throws Exception
     */
    private function isFinishStateWithSingleEnteringTransition(string $mode, int $stateOut): bool
    {
        if (!$this->getDfa($mode)->getStateMap()->isFinishState($stateOut)) {
            return false;
        }
        $enters = $this->getDfa($mode)->getTransitionMap()->getEnterList($stateOut);
        $exits = $this->getDfa($mode)->getTransitionMap()->getExitList($stateOut);
        if (!(count($enters) == 1 && count($exits) == 0)) {
            return false;
        }
        $symbolList = array_pop($enters);

        return count($symbolList) == 1;
    }

    private function buildHex(int $char): string
    {
        $hexChar = strtoupper(dechex($char));
        if (strlen($hexChar) % 2 != 0) {
            $hexChar = "0{$hexChar}";
        }

        return "0x{$hexChar}";
    }

    private function buildRangeCondition(Range $range): array
    {
        $startChar = $this->buildHex($range->getStart());
        if ($range->getStart() == $range->getFinish()) {
            return ["{$startChar} == \$char"];
        }
        $finishChar = $this->buildHex($range->getFinish());
        if ($range->getStart() + 1 == $range->getFinish()) {
            return [
                "{$startChar} == \$char",
                "{$finishChar} == \$char",
            ];
        }

        return ["{$startChar} <= \$char && \$char <= {$finishChar}"];
    }

    private function buildRangeSetCondition(RangeSet $rangeSet): string
    {
        $conditionList = [];
        foreach ($rangeSet->getRanges() as $range) {
            $conditionList = array_merge($conditionList, $this->buildRangeCondition($range));
        }
        $result = implode(" || ", $conditionList);
        if (strlen($result) + 15 <= 120 || count($conditionList) == 1) {
            return ltrim($result);
        }
        $result = $this->buildMethodPart(implode(" ||\n", $conditionList), 1);

        return "\n    " . ltrim($result);
    }

    /**
     * @param string $mode
     * @param int    $stateIn
     * @return string
     * @throws Exception
     */
    private function buildStateFinish(string $mode, int $stateIn): string
    {
        if (!$this->getDfa($mode)->getStateMap()->isFinishState($stateIn)) {
            return $this->buildMethodPart("goto error;\n");
        }
        $result = '';
        if (!empty($this->getDfa($mode)->getTransitionMap()->getExitList($stateIn))) {
            $result .= $this->buildMethodPart("{$this->buildStateLabel('finish', $mode, $stateIn)}:");
        }
        $result .= "{$this->buildToken($mode, $stateIn)}\n";

        return $result;
    }

    /**
     * @param string $mode
     * @param int    $stateIn
     * @param int    $indent
     * @return string
     * @throws Exception
     */
    private function buildToken(string $mode, int $stateIn, int $indent = 2): string
    {
        $result = '';
        $regExpCount = 0;
        $defaultRegExp = null;
        foreach ($this->visitedHashMap[$mode][$stateIn] ?? [] as $regExp => $visitedHashes) {
            if (empty($visitedHashes)) {
                $defaultRegExp = (string) $regExp;
                $regExpCount++;
                continue;
            }
            $visitedHashValues = [];
            foreach ($visitedHashes as $visitedHash) {
                $visitedHashValues[] = var_export($visitedHash, true);
            }
            $visitedHashArgs = implode(', ', $visitedHashValues);
            $tokenSpec = $this->spec->getTokenSpec($mode, (string) $regExp);
            $result .=
                $this->buildMethodPart("if (\$context->isVisitedTransition({$visitedHashArgs})) {", $indent) .
                $this->buildMethodPart("// {$regExp}", $indent + 1) .
                $this->buildSingleToken($tokenSpec, $indent + 1) .
                $this->buildMethodPart("}", $indent);
            $regExpCount++;
        }

        if (0 == $regExpCount) {
            throw new Exception("No tokens found for state {$stateIn}");
        }

        if (isset($defaultRegExp)) {
            $tokenSpec = $this->spec->getTokenSpec($mode, $defaultRegExp);

            return
                $result .
                $this->buildMethodPart("// {$defaultRegExp}", $indent) .
                $this->buildSingleToken($tokenSpec, $indent);
        }

        return
            $result .
            $this->buildMethodPart("goto error;", $indent);
    }

    private function buildSingleToken(TokenSpec $tokenSpec, int $indent): string
    {
        return
            $this->buildMethodPart($tokenSpec->getCode(), $indent) .
            $this->buildOnToken($indent) . "\n" .
            $this->buildMethodPart("return true;", $indent);
    }

    private function buildErrorState(): string
    {
        $code = $this->spec->getOnError();

        return
            $this->buildMethodPart("error:") .
            $this->buildMethodPart('' == $code ? "return false;" : $code);
    }

    private function buildMethodPart(string $code, int $indent = 2): string
    {
        if ('' == $code) {
            return '';
        }
        $result = '';
        $codeLineList = explode("\n", $code);
        foreach ($codeLineList as $codeLine) {
            $line = '';
            for ($i = 0; $i < $indent; $i++) {
                $line .= "    ";
            }
            $result .= rtrim($line . $codeLine) . "\n";
        }

        return $result;
    }

    private function buildOnTransition(): string
    {
        return $this->buildMethodPart($this->spec->getOnTransition(), 3);
    }

    private function buildOnToken(int $indent = 2): string
    {
        return $this->buildMethodPart($this->spec->getOnToken(), $indent);
    }

    /**
     * @param string $context
     * @return Dfa
     * @throws Exception
     */
    private function getDfa(string $context): Dfa
    {
        if (!isset($this->dfa[$context])) {
            $this->dfa[$context] = $this->buildDfa($context);
        }

        return $this->dfa[$context];
    }

    /**
     * @param string $mode
     * @return Dfa
     * @throws Exception
     */
    private function buildDfa(string $mode): Dfa
    {
        $nfa = new Nfa();
        $startState = $nfa->getStateMap()->createState();
        $nfa->getStateMap()->addStartState($startState);
        $nfaRegExpMap = [];
        foreach ($this->spec->getTokenSpecList($mode) as $tokenSpec) {
            $existingStates = $nfa->getStateMap()->getStateList();
            $regExpEntryState = $nfa->getStateMap()->createState();
            $nfa
                ->getEpsilonTransitionMap()
                ->addTransition($startState, $regExpEntryState, true);
            $this->buildRegExp($nfa, $regExpEntryState, $tokenSpec->getRegExp());
            $nfaRegExpMap[$tokenSpec->getRegExp()] = array_diff(
                $nfa->getStateMap()->getStateList(),
                $existingStates
            );
        }

        $dfa = DfaBuilder::fromNfa($nfa);
        $dfaRegExpMap = [];
        foreach (array_keys($nfaRegExpMap, null, true) as $regExp) {
            $dfaRegExpMap[$regExp] = [];
        }
        $allDfaStateIds = $dfa->getStateMap()->getStateList();
        foreach ($allDfaStateIds as $dfaStateId) {
            $nfaStateIds = $dfa->getStateMap()->getStateValue($dfaStateId);
            foreach ($nfaRegExpMap as $regExp => $nfaRegExpStateIds) {
                if (!empty(array_intersect($nfaStateIds, $nfaRegExpStateIds))) {
                    $dfaRegExpMap[(string) $regExp][] = $dfaStateId; // TODO: why the hell integer?..
                }
            }
        }
        $this->regExpMap[$mode] = [];
        foreach ($dfaRegExpMap as $regExp => $regExpStateIds) {
            $this->regExpMap[$mode][(string) $regExp] = [$regExpStateIds, array_diff($allDfaStateIds, $regExpStateIds)];
        }
        $nfaRegExpTransitionMap = new TransitionMap($nfa->getStateMap());
        foreach ($nfa->getSymbolTransitionMap()->getTransitionList() as $nfaSourceStateId => $nfaTransitionTargets) {
            foreach ($nfaTransitionTargets as $nfaTargetStateId => $nfaSymbolIds) {
                $regExps = [];
                foreach ($nfaRegExpMap as $regExp => $nfaRegExpIds) {
                    if (in_array($nfaSourceStateId, $nfaRegExpIds) || in_array($nfaTargetStateId, $nfaRegExpIds)) {
                        $regExps[] = (string) $regExp;
                    }
                }
                $nfaTransitionValue = [];
                foreach ($nfaSymbolIds as $nfaSymbolId) {
                    $nfaTransitionValue[$nfaSymbolId] = $regExps;
                }
                $nfaRegExpTransitionMap->addTransition($nfaSourceStateId, $nfaTargetStateId, $nfaTransitionValue);
            }
        }

        $map = [];
        $mapIn = [];
        $mapOut = [];
        foreach ($dfa->getTransitionMap()->getTransitionList() as $dfaSourceStateId => $dfaTransitionTargets) {
            foreach ($dfaTransitionTargets as $dfaTargetStateId => $dfaSymbolIds) {
                $matchingNfaSourceStateIds = $dfa->getStateMap()->getStateValue($dfaSourceStateId);
                $matchingNfaTargetStateIds = $dfa->getStateMap()->getStateValue($dfaTargetStateId);
                $dfaTransitionValue = [];
                foreach ($matchingNfaSourceStateIds as $nfaSourceStateId) {
                    foreach ($matchingNfaTargetStateIds as $nfaTargetStateId) {
                        if (
                            $nfa->getStateMap()->stateExists($nfaSourceStateId) && // TODO: find out invalid id
                            $nfa->getStateMap()->stateExists($nfaTargetStateId) &&
                            $nfaRegExpTransitionMap->transitionExists($nfaSourceStateId, $nfaTargetStateId)
                        ) {
                            $nfaTransitionValue = $nfaRegExpTransitionMap->getTransition(
                                $nfaSourceStateId,
                                $nfaTargetStateId
                            );
                            foreach ($dfaSymbolIds as $dfaSymbolId) {
                                if (isset($nfaTransitionValue[$dfaSymbolId])) {
                                    $dfaTransitionValue[$dfaSymbolId] = array_unique(
                                        array_merge(
                                            $dfaTransitionValue[$dfaSymbolId] ?? [],
                                            $nfaTransitionValue[$dfaSymbolId]
                                        )
                                    );
                                }
                            }
                        }
                    }
                }
                foreach ($dfaTransitionValue as $dfaSymbolId => $regExps) {
                    $hash = $this->buildHash($dfaSourceStateId, $dfaTargetStateId, $dfaSymbolId);
                    $map[$hash] = $regExps;
                    $mapIn[$dfaSourceStateId] = array_merge($mapIn[$dfaSourceStateId] ?? [], [$hash]);
                    $mapOut[$dfaTargetStateId] = array_merge($mapOut[$dfaTargetStateId] ?? [], [$hash]);
                }
            }
        }
        $incomingTransitionsForHash = [];
        $incomingTransitionsForState = [];
        foreach ($mapIn as $stateId => $hashes) {
            foreach ($hashes as $hash) {
                $incomingTransitionsForHash[$hash] = $mapOut[$stateId] ?? [];
            }
        }
        foreach (array_keys($mapOut) as $stateId) {
            if ($dfa->getStateMap()->isFinishState($stateId)) {
                $incomingTransitionsForState[$stateId] = $mapOut[$stateId] ?? [];
            }
        }
        $this->visitedHashMap[$mode] = [];
        foreach ($incomingTransitionsForState as $stateId => $hashes) {
            $inHashBuffer = $hashes;
            $processedHashes = [];
            $visitedHashes = [];
            $regExps = [];
            foreach ($hashes as $hash) {
                $regExps = array_unique(array_merge($regExps, $map[$hash]));
            }
            while (!empty($inHashBuffer)) {
                $inHash = array_pop($inHashBuffer);
                if (isset($processedHashes[$inHash])) {
                    continue;
                }
                $processedHashes[$inHash] = true;
                $inRegExps = array_intersect($map[$inHash], $regExps);
                if (count($inRegExps) == 1) {
                    $inRegExp = $inRegExps[array_key_first($inRegExps)];
                    $visitedHashes[$inRegExp][] = $inHash;
                    continue;
                }
                array_push($inHashBuffer, ...($incomingTransitionsForHash[$inHash] ?? []));
            }
            $this->visitedHashMap[$mode][$stateId] = $visitedHashes;
        }
        foreach ($this->visitedHashMap[$mode] as $stateId => $visitedHashes) {
            if (!empty($visitedHashes)) {
                continue;
            }
            $regExps = [];
            foreach ($incomingTransitionsForState[$stateId] ?? [] as $hash) {
                $regExps = array_unique(array_merge($map[$hash], $regExps));
            }
            $otherRegExps = [];
            foreach ($this->visitedHashMap[$mode] as $anotherStateId => $otherVisitedHashes) {
                if ($stateId == $anotherStateId) {
                    continue;
                }
                foreach ($otherVisitedHashes as $otherRegExp => $otherHashes) {
                    $otherRegExps = array_unique(array_merge($otherRegExps, [$otherRegExp]));
                }
            }
            $regExps = array_diff($regExps, $otherRegExps);
            if (count($regExps) == 1) {
                $regExp = $regExps[array_key_first($regExps)];
                $this->visitedHashMap[$mode][$stateId][$regExp] = [];
            }
        }

        return $dfa;
    }

    private function buildHash(int $stateIn, int $stateOut, int $symbol): string
    {
        return "{$stateIn}-{$stateOut}:{$symbol}";
    }

    /**
     * @param Nfa    $nfa
     * @param int    $entryState
     * @param string $regExp
     * @throws Exception
     */
    private function buildRegExp(Nfa $nfa, int $entryState, string $regExp): void
    {
        $buffer = CharBufferFactory::createFromString($regExp);
        $tree = new Tree();
        ParserFactory::createFromBuffer($tree, $buffer)->run();
        $nfaBuilder = new NfaBuilder($nfa, PropertyLoader::create());
        $nfaBuilder->setStartState($entryState);
        (new Translator($tree, $nfaBuilder))->run();
    }
}
