<?php

namespace App\OutdatedLibraries\OutdatedLibrariesMarkdown;

use App\OutdatedLibraries\OutdatedFileToTable\Library;

trait PatternTrait
{
    private function generateHeader(string $name): array
    {
        return ["| $name | version  | version disponible |", '| --- | --- | --- |'];
    }

    private function patternLigne(Library $value): string
    {
        $name = $value->getName();
        $installedVersion = $value->getInstalledVersion();
        $latestVersion = $value->getLatestVersion();

        return '| ⚠️ '." $name  | $installedVersion | $latestVersion |";
    }
}
