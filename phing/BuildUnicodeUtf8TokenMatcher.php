<?php

class BuildUnicodeUtf8TokenMatcher extends Task
{

    private $configFile;

    private $output;

    private $usedClassList;

    public function init(): void
    {
        $this->output = '';
        $this->usedClassList = [];
    }

    /**
     * @throws ReflectionException
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
        $this->usedClassList[] = $parentClass->getName();
        foreach ($config['use'] ?? [] as $usedClass) {
            $this->usedClassList[] = $usedClass;
        }
        $this->output .= $this->buildUsedClassList();
        $this->output .= PHP_EOL . "class {$class} extends {$parentClass->getShortName()}" . PHP_EOL;
        $this->output .= "{" . PHP_EOL;

        $matchMethod = $parentClass->getMethod('match');

        $this->output .= "}" . PHP_EOL;
        //var_dump($this->output);
        $lineCount = count(explode(PHP_EOL, $this->output));
        $this->log("Done ({$lineCount} lines)!");
    }

    public function setConfigFile(string $configFile): void
    {
        $this->configFile = $configFile;
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

    private function buildUsedClassList(): string
    {
        $output = '';
        sort($this->usedClassList);
        if (!empty($this->usedClassList)) {
            $output .= PHP_EOL;
            foreach ($this->usedClassList as $usedClass) {
                $output .= "use {$usedClass};" . PHP_EOL;
            }
        }
        return $output;
    }
}
