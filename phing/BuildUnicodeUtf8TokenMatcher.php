<?php

use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\RegExp\FSM\DfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\Nfa;
use Remorhaz\UniLex\RegExp\FSM\NfaBuilder;
use Remorhaz\UniLex\RegExp\ParserFactory;
use Remorhaz\UniLex\TokenMatcherInterface;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

class BuildUnicodeUtf8TokenMatcher extends Task
{

    private $configFile;

    private $outputFile;

    private $output;

    private $usedClassList;

    public function init(): void
    {
        $this->output = '';
        $this->usedClassList = [];
    }

    /**
     * @throws ReflectionException
     * @throws \Remorhaz\UniLex\Exception
     */
    public function main(): void
    {
        $config = $this->getConfig();
        $this->log("Generating matcher class...");
        $fullClass = $config['class'];
        $fullClassParts = explode('\\', $fullClass);
        $class = array_pop($fullClassParts);
        $namespace = implode('\\', $fullClassParts);

        $this->output .= "<?php" . PHP_EOL;
        $this->output .= PHP_EOL . "namespace {$namespace};" . PHP_EOL;
        $parentClass = new ReflectionClass($config['template_class']);
        if (!$parentClass->implementsInterface(TokenMatcherInterface::class)) {
            $this->log(
                "Template class {$parentClass->getName()} must implement" . TokenMatcherInterface::class,
                Project::MSG_ERR
            );
            throw new BuildException("Error generating matcher class!");
        }
        $this->usedClassList[] = $parentClass->getName();
        foreach ($config['use'] ?? [] as $usedClass) {
            $this->usedClassList[] = $usedClass;
        }
        $matchMethod = $parentClass->getMethod('match');
        foreach ($matchMethod->getParameters() as $matchParameter) {
            if (!$matchParameter->hasType() || $matchParameter->getType()->isBuiltin()) {
                continue;
            }
            $matchParameterClass = new ReflectionClass($matchParameter->getType()->getName());
            $this->usedClassList[] = $matchParameterClass->getName();
        }
        $this->output .= $this->buildUsedClassList($namespace);
        $this->output .= PHP_EOL . "class {$class} extends {$parentClass->getShortName()}" . PHP_EOL;
        $this->output .= "{" . PHP_EOL;

        $this->output .= PHP_EOL . "    public function {$matchMethod->getName()}(";
        $paramList = [];
        foreach ($matchMethod->getParameters() as $matchParameter) {
            if ($matchParameter->hasType()) {
                $param = $matchParameter->getType()->isBuiltin()
                    ? $matchParameter->getType()->getName()
                    : (new ReflectionClass($matchParameter->getType()->getName()))->getShortName();
                $param .= " ";
            } else {
                $param = "";
            }
            $param .= "\${$matchParameter->getName()}";
            $paramList[] = $param;
        }
        $this->output .= implode(", ", $paramList);
        $this->output .= "): bool" . PHP_EOL;
        $this->output .= "    {" . PHP_EOL;

        foreach ($config['before_match'] ?? [] as $codeString) {
            $this->output .= "        " . trim($codeString) . PHP_EOL;
        }

        $nfa = new Nfa;
        $startState = $nfa->getStateMap()->createState();
        $nfa->getStateMap()->setStartState($startState);
        $oldFinishStates = [];
        $tokenNfaStateMap = [];
        foreach ($config['token_list'] as $regExp => $tokenData) {
            $regExpEntryState = $nfa->getStateMap()->createState();
            $nfa
                ->getEpsilonTransitionMap()
                ->addTransition($startState, $regExpEntryState, true);
            $buffer = CharBufferFactory::createFromUtf8String($regExp);
            $tree = new Tree;
            ParserFactory::createFromBuffer($tree, $buffer)->run();
            $nfaBuilder = new NfaBuilder($nfa);
            $nfaBuilder->setStartState($regExpEntryState);
            (new Translator($tree, $nfaBuilder))->run();
            $finishStates = $nfa->getStateMap()->getFinishStateList();
            $newFinishStates = array_diff($finishStates, $oldFinishStates);
            $tokenType = array_shift($tokenData);
            foreach ($newFinishStates as $finishState) {
                $tokenNfaStateMap[$finishState] = [$tokenType, $tokenData];
            }
            $oldFinishStates = $finishStates;
        }
        $dfa = DfaBuilder::fromNfa($nfa);
        $this->output .= "        goto state{$dfa->getStateMap()->getStartState()};" . PHP_EOL . PHP_EOL;
        foreach ($dfa->getStateMap()->getStateList() as $stateIn) {
            $this->output .= "        state{$stateIn}:" . PHP_EOL;
            $moves = $dfa->getTransitionMap()->findMoves($stateIn);
            if (!empty($moves)) {
                $this->output .= "        \$char = \$buffer->getSymbol();" . PHP_EOL;
            }
            foreach ($moves as $stateOut => $symbolList) {
                foreach ($symbolList as $symbol) {
                    $rangeSet = $dfa->getSymbolTable()->getRangeSet($symbol);
                    $conditionList = [];
                    foreach ($rangeSet->getRanges() as $range) {
                        $conditionList[] = $range->getStart() == $range->getFinish()
                            ? "\$char == {$range->getStart()}"
                            : "{$range->getStart()} <= \$char && \$char <= {$range->getFinish()}";
                    }
                    $condition = implode(" || ", $conditionList);
                    $this->output .= "        if ({$condition}) {" . PHP_EOL;
                    foreach ($config['on_transition'] ?? [] as $codeString) {
                        $this->output .= "            " . trim($codeString) . PHP_EOL;
                    }
                    $this->output .= "            \$buffer->nextSymbol();" . PHP_EOL;
                    $this->output .= "            goto state{$stateOut};" . PHP_EOL;
                    $this->output .= "        }" . PHP_EOL;
                }
            }
            if ($dfa->getStateMap()->isFinishState($stateIn)) {
                $tokenData = null;
                foreach ($dfa->getStateMap()->getStateValue($stateIn) as $nfaFinishState) {
                    if (isset($tokenNfaStateMap[$nfaFinishState])) {
                        $tokenData = $tokenNfaStateMap[$nfaFinishState];
                        break;
                    }
                }
                if (!isset($tokenData)) {
                    $this->log("No token data found for finish state {$stateIn}", Project::MSG_ERR);
                    throw new BuildException("Error generating matcher class!");
                }
                [$tokenType, $codeStringList] = $tokenData;
                $this->output .= "        \$tokenType = {$tokenType};" . PHP_EOL;
                $codeStringList = array_merge($config['on_token'] ?? [], $codeStringList);
                foreach ($codeStringList as $codeString) {
                    $this->output .= "        " . trim($codeString) . PHP_EOL;
                }
                $this->output .= "        return true;" . PHP_EOL . PHP_EOL;
                continue;
            }
            $this->output .= "        goto error;" . PHP_EOL . PHP_EOL;
        }
        $this->output .= "        error:" . PHP_EOL;
        foreach ($config['on_error'] ?? ["return false;"] as $codeString) {
            $this->output .= "        " . trim($codeString) . PHP_EOL;
        }

        $this->output .= "    }" . PHP_EOL;

        $this->output .= "}" . PHP_EOL;
        $lineCount = count(explode(PHP_EOL, $this->output));
        $this->log("Done ({$lineCount} lines)!");

        $this->log("Dumping generated data to file {$this->outputFile}...");
        $result = file_put_contents($this->outputFile, $this->output);
        if (false === $result) {
            $this->log("Failed to dump matcher data to file {$this->outputFile}", Project::MSG_ERR);
            return;
        }
        $this->log("Done ({$result} bytes)!");

    }

    public function setConfigFile(string $configFile): void
    {
        $this->configFile = $configFile;
    }

    public function setOutputFile(string $outputFile): void
    {
        $this->outputFile = $outputFile;
    }

    private function getConfig(): array
    {
        if (!isset($this->configFile)) {
            $this->log(
                "Config file is not defined. Use 'configFile' attribute to pass the file name!",
                Project::MSG_ERR
            );
            throw new BuildException("Error reading matcher config");
        }
        $this->log("Reading matcher config from {$this->configFile}...");
        /** @noinspection PhpIncludeInspection */
        $config = @include_once $this->configFile;
        if (false === $config) {
            $this->log("Failed to include config file!", Project::MSG_ERR);
            throw new BuildException("Error reading matcher config");
        }
        $this->log("Done!");
        return $config;
    }

    /**
     * @param string $namespace
     * @return string
     * @throws ReflectionException
     */
    private function buildUsedClassList(string $namespace): string
    {
        $output = '';
        sort($this->usedClassList);
        if (!empty($this->usedClassList)) {
            $output .= PHP_EOL;
            foreach ($this->usedClassList as $usedClass) {
                $class = new ReflectionClass($usedClass);
                if ($class->getNamespaceName() != $namespace) {
                    $output .= "use {$usedClass};" . PHP_EOL;
                }
            }
        }
        return $output;
    }
}
