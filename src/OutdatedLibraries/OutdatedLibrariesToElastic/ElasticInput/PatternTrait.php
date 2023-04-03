<?php

namespace App\OutdatedLibraries\OutdatedLibrariesToElastic\ElasticInput;

use App\OutdatedLibraries\OutdatedFileToTable\Library;

trait PatternTrait
{
    private function patternArray(string $project, Library $value): array
    {
        $name = $value->getName();
        $installedVersion = $value->getInstalledVersion();
        $latestVersion = $value->getLatestVersion();

        return ['project' => $project, 'library' => $name, 'version' => $installedVersion, 'latestVersion' => $latestVersion];
    }
}
