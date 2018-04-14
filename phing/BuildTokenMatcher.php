<?php

use Remorhaz\UniLex\Lexer\TokenMatcherGenerator;
use Remorhaz\UniLex\Lexer\TokenMatcherSpec;
use Remorhaz\UniLex\Lexer\TokenSpec;

class BuildTokenMatcher extends Task
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

        $spec = new TokenMatcherSpec($config['class'], $config['template_class']);
        $spec
            ->addFileComment(...$this->getFileCommentLines())
            ->setBeforeMatch(implode("\n", $config['before_match'] ?? []))
            ->setOnError(implode("\n", $config['on_error'] ?? []))
            ->setOnTransition(implode("\n", $config['on_transition'] ?? []))
            ->setOnToken(implode("\n", $config['on_token'] ?? []));
        foreach ($config['use'] ?? [] as $usedClassName) {
            $spec->addUsedClass($usedClassName);
        }
        foreach ($config['token_list'] as $regExp => $tokenData) {
            $code = implode("\n", $tokenData);
            $tokenSpec = new TokenSpec($regExp, $code);
            $spec->addTokenSpec($tokenSpec);
        }
        $generator = new TokenMatcherGenerator($spec);
        $output = $generator->getOutput();
        $lineCount = count(explode("\n", $output));
        $this->log("Done ({$lineCount} lines)!");

        $this->log("Dumping generated data to file {$this->outputFile}...");
        $result = file_put_contents($this->outputFile, $output);
        if (false === $result) {
            $this->log("Failed to dump matcher data to file {$this->outputFile}", Project::MSG_ERR);
            throw new BuildException("Error dumping matcher data to file");
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

    private function getFileCommentLines(): array
    {
        return [
            $this->getDescription(),
            "",
            "Auto-generated file, please don't edit manually.",
            "Run following command to update this file:",
            "    vendor/bin/phing {$this->getOwningTarget()->getName()}",
            "",
            "Phing version: {$this->getProject()->getPhingVersion()}"
        ];
    }
}
