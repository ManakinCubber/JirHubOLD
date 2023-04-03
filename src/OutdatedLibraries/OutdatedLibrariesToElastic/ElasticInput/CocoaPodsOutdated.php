<?php

namespace App\OutdatedLibraries\OutdatedLibrariesToElastic\ElasticInput;

use App\OutdatedLibraries\OutdatedFileToTable\OutdatedFileToTable;

class CocoaPodsOutdated
{
    use PatternTrait;

    public function __construct(private readonly OutdatedFileToTable $OutdatedFileToTable)
    {
    }

    public function getCocoaPodsJson(string $path, string $name): string
    {
        $tab = $this->OutdatedFileToTable->cocoaPodsOutdatedTable($path);

        foreach ($tab as $key => $value) {
            $tab[$key] = $this->patternArray($name, $value);
        }
        $now = (new \DateTimeImmutable())->format(\DateTimeInterface::RFC3339);
        $tab[] = ['@timestamp' => $now];

        return json_encode(array_values($tab));
    }
}
