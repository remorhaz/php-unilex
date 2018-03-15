<?php

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Parser\LL1\Lookup\TableBuilder;
use Remorhaz\UniLex\RegExp\Grammar\ConfigFile;

class BuildRegExpLookupTable extends Task
{

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
        $exportedMap = var_export($map, true);
        $data = "<?php\n\nreturn {$exportedMap};\n";
        $this->log("Done!");

        $targetFile = ConfigFile::getLookupTablePath();
        $this->log("Dumping generated data to file {$targetFile}...");
        $result = file_put_contents($targetFile, $data);
        if (false === $result) {
            $this->log("Failed to dump RegExp lookup table to file {$targetFile}", Project::MSG_ERR);
            return;
        }
        $this->log("Done ({$result} bytes)!");
    }
}
