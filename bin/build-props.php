<?php

declare(strict_types=1);

use Remorhaz\UniLex\RegExp\PropertyBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

echo "Building Unicode properties\n";
$propertyBuilder = new PropertyBuilder();
echo " + Building unicode data...\n";
$index = $propertyBuilder->buildUnicodeData([]);
echo " + Building scripts...\n";
$index = $propertyBuilder->buildScripts($index);
echo " + Dumping index...\n";
$propertyBuilder->dumpIndex($index);
echo "Done!";
