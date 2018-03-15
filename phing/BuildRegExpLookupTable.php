<?php

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Parser\LL1\Lookup\TableBuilder;
use Remorhaz\UniLex\RegExp\Grammar\ConfigFile;
use Remorhaz\UniLex\RegExp\Grammar\SymbolType;
use Remorhaz\UniLex\RegExp\Grammar\TokenType;

class BuildRegExpLookupTable extends Task
{

    /**
     * @var ReflectionClass
     */
    private $symbolClass;

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
     * @throws ReflectionException
     */
    public function init()
    {
        $this->symbolClass = new ReflectionClass(SymbolType::class);
        $this->tokenClass = new ReflectionClass(TokenType::class);
        $this->symbolConstantList = $this
            ->symbolClass
            ->getConstants();
        $this->tokenConstantList = $this
            ->tokenClass
            ->getConstants();
    }

    /**
     * @throws Exception
     */
    public function main()
    {
        $this->log("Generating lookup table...");
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $map = (new TableBuilder($grammar))
            ->getTable()
            ->exportMap();
        $data =
            "<?php\n{$this->buildFileComment()}\n" .
            "use {$this->symbolClass->getName()};\n" .
            "use {$this->tokenClass->getName()};\n\n" .
            "return [\n{$this->buildTableMap($map)}];\n";
        $this->log("Done ({$this->buildTableInfo()})!");

        $targetFile = ConfigFile::getLookupTablePath();
        $this->log("Dumping generated data to file {$targetFile}...");
        $result = file_put_contents($targetFile, $data);
        if (false === $result) {
            $this->log("Failed to dump RegExp lookup table to file {$targetFile}", Project::MSG_ERR);
            return;
        }
        $this->log("Done ({$result} bytes)!");
    }

    private function getSymbol(int $symbolId): string
    {
        $symbolSearch = array_search($symbolId, $this->symbolConstantList);
        return false === $symbolSearch
            ? (string) $symbolId
            : "{$this->symbolClass->getShortName()}::{$symbolSearch}";
    }

    private function getToken(int $tokenId): string
    {
        $tokenSearch = array_search($tokenId, $this->tokenConstantList);
        return false === $tokenSearch
            ? (string) $tokenId
            : "{$this->tokenClass->getShortName()}::{$tokenSearch}";
    }

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
            " * RegExp lookup table for usage with LL(1) parser.\n" .
            " *\n" .
            " * Auto-generated file, please don't edit manually.\n" .
            " * Run following command to update this file:\n" .
            " *     vendor/bin/phing {$this->getOwningTarget()->getName()}\n" .
            " *\n" .
            " * Phing version: {$this->getProject()->getPhingVersion()}\n" .
            " */\n";
    }
}
