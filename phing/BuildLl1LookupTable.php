<?php

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Parser\LL1\Lookup\TableBuilder;

class BuildLl1LookupTable extends Task
{

    private $configFile;

    /**
     * @var PhingFile
     */
    private $destFile;

    private $symbolClassName;

    /**
     * @var ReflectionClass
     */
    private $symbolClass;

    private $tokenClassName;

    /**
     * @var ReflectionClass
     */
    private $tokenClass;

    private $symbolConstantList;

    private $tokenConstantList;

    private $knownSymbolList = [];

    private $knownTokenList = [];

    private $validCellCount = 0;

    /**
     * @throws Exception
     * @throws IOException
     * @throws ReflectionException
     */
    public function main()
    {
        $this->log("Generating LL(1) lookup table...");
        $grammar = GrammarLoader::loadFile($this->configFile);
        $map = (new TableBuilder($grammar))
            ->getTable()
            ->exportMap();
        $data =
            "<?php\n\n{$this->buildFileComment()}\n";
        if (isset($this->symbolClassName)) {
            $data .= "use {$this->symbolClassName};\n";
        }
        if (isset($this->tokenClassName)) {
            $data .= "use {$this->tokenClassName};\n";
        }
        if (isset($this->tokenClassName) || isset($this->symbolClassName)) {
            $data .= "\n";
        }
        $data .= "return [\n{$this->buildTableMap($map)}];\n";
        $this->log("Done ({$this->buildTableInfo()})!");

        $this->log("Dumping generated data to file {$this->destFile}...");
        $writer = new FileWriter($this->destFile);
        $writer->write($data);
        $writer->close();
        $this->log("Done ({$this->destFile->length()} bytes)!");
    }

    public function setSymbolClassName(string $name): void
    {
        $this->symbolClassName = $name;
    }

    public function setTokenClassName(string $name): void
    {
        $this->tokenClassName = $name;
    }

    public function setConfigFile(string $fileName): void
    {
        $this->configFile = $fileName;
    }

    public function setDestFile(PhingFile $fileName): void
    {
        $this->destFile = $fileName;
    }

    /**
     * @param int $symbolId
     * @return string
     * @throws ReflectionException
     */
    private function getSymbol(int $symbolId): string
    {
        $symbolSearch = array_search($symbolId, $this->getSymbolConstantList());
        return false === $symbolSearch || !isset($this->symbolClassName)
            ? (string) $symbolId
            : "{$this->getSymbolClass()->getShortName()}::{$symbolSearch}";
    }

    /**
     * @param int $tokenId
     * @return string
     * @throws ReflectionException
     */
    private function getToken(int $tokenId): string
    {
        $tokenSearch = array_search($tokenId, $this->getTokenConstantList());
        return false === $tokenSearch
            ? (string) $tokenId
            : "{$this->getTokenClass()->getShortName()}::{$tokenSearch}";
    }

    /**
     * @param array $map
     * @return string
     * @throws ReflectionException
     */
    private function buildTableMap(array $map): string
    {
        $data = '';
        $margin = "    ";
        foreach ($map as $symbolId => $productionMap) {
            if (!isset($this->knownSymbolList[$symbolId])) {
                $this->knownSymbolList[$symbolId] = true;
            }
            $builtProductionMap = $this->buildProductionMap($productionMap);
            $data .= "{$margin}{$this->getSymbol($symbolId)} => [\n{$builtProductionMap}{$margin}],\n";
        }
        return $data;
    }

    /**
     * @param array $productionMap
     * @return string
     * @throws ReflectionException
     */
    private function buildProductionMap(array $productionMap): string
    {
        $data = '';
        $margin = "        ";
        foreach ($productionMap as $tokenId => $productionIndex) {
            if (!isset($this->knownTokenList[$tokenId])) {
                $this->knownTokenList[$tokenId] = true;
            }
            $this->validCellCount++;
            $data .= "{$margin}{$this->getToken($tokenId)} => {$productionIndex},\n";
        }
        return $data;
    }

    private function buildTableInfo(): string
    {
        $knownSymbolCount = count($this->knownSymbolList);
        $knownTokenCount = count($this->knownTokenList);
        $totalCellCount = $knownSymbolCount * $knownTokenCount;
        return
            "size: {$knownSymbolCount}x{$knownTokenCount}, " .
            "valid cells: {$this->validCellCount}/{$totalCellCount}";
    }

    private function buildFileComment(): string
    {
        return
            "/**\n" .
            " * {$this->getDescription()}\n" .
            " *\n" .
            " * Auto-generated file, please don't edit manually.\n" .
            " * Run following command to update this file:\n" .
            " *     vendor/bin/phing {$this->getOwningTarget()->getName()}\n" .
            " *\n" .
            " * Phing version: {$this->getProject()->getPhingVersion()}\n" .
            " */\n";
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    private function getSymbolConstantList(): array
    {
        if (!isset($this->symbolConstantList)) {
            $this->symbolConstantList = isset($this->symbolClassName)
                ? $this->getSymbolClass()->getConstants()
                : [];
        }
        return $this->symbolConstantList;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    private function getTokenConstantList(): array
    {
        if (!isset($this->tokenConstantList)) {
            $this->tokenConstantList = isset($this->tokenClassName)
                ? $this->getTokenClass()->getConstants()
                : [];
        }
        return $this->tokenConstantList;
    }

    /**
     * @return ReflectionClass
     * @throws ReflectionException
     */
    private function getSymbolClass(): ReflectionClass
    {
        if (!isset($this->symbolClass)) {
            if (!isset($this->symbolClassName)) {
                throw new BuildException("Symbol class is not defined");
            }
            $this->symbolClass = new ReflectionClass($this->symbolClassName);
        }
        return $this->symbolClass;
    }

    /**
     * @return ReflectionClass
     * @throws ReflectionException
     */
    private function getTokenClass(): ReflectionClass
    {
        if (!isset($this->tokenClass)) {
            if (!isset($this->tokenClassName)) {
                throw new BuildException("Token class is not defined");
            }
            $this->tokenClass = new ReflectionClass($this->tokenClassName);
        }
        return $this->tokenClass;
    }
}
