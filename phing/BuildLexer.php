<?php

use Remorhaz\UniLex\Lexer\TokenMatcherGenerator;
use Remorhaz\UniLex\Lexer\TokenMatcherSpecParser;

class BuildLexer extends Task
{

    /**
     * @var PhingFile
     */
    private $destFile;

    private $sourceFile;

    public function setDestFile(PhingFile $file): void
    {
        $this->destFile = $file;
    }

    public function setSourceFile(PhingFile $file): void
    {
        $this->sourceFile = $file;
    }

    public function init()
    {
    }

    /**
     * @throws ReflectionException
     * @throws \Remorhaz\UniLex\Exception
     */
    public function main()
    {
        if (!isset($this->sourceFile)) {
            throw new BuildException("Source file is not defined");
        }
        if (!isset($this->destFile)) {
            throw new BuildException("Destination file is not defined");
        }

        $this->log("Generating matcher class...");
        $matcherSpec = TokenMatcherSpecParser::loadFromFile((string) $this->sourceFile)
            ->getMatcherSpec()
            ->addFileComment(...$this->getFileCommentLines());
        $generator = new TokenMatcherGenerator($matcherSpec);
        $output = $generator->getOutput();
        $lineCount = count(explode("\n", $output));
        $this->log("Done ({$lineCount} lines)!");

        $this->log("Dumping generated data to file {$this->destFile}...");
        $writer = new FileWriter($this->destFile);
        $writer->write($output);
        $writer->close();
        $byteCount = strlen($output);
        $this->log("Done ({$byteCount} bytes)!");
    }

    private function getFileCommentLines(): array
    {
        $result = [
            "Auto-generated file, please don't edit manually.",
            "Run following command to update this file:",
            "    vendor/bin/phing {$this->getOwningTarget()->getName()}",
            "",
            "Phing version: {$this->getProject()->getPhingVersion()}"
        ];
        $description = $this->getDescription();
        if (isset($description)) {
            array_unshift($result, $description, "");
        }
        return $result;
    }
}
