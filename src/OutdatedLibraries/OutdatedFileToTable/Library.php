<?php

namespace App\OutdatedLibraries\OutdatedFileToTable;

readonly class Library
{
    public function __construct(
        private string $name,
        private string $installedVersion,
        private string $latestVersion
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInstalledVersion(): string
    {
        return $this->installedVersion;
    }

    public function getLatestVersion(): string
    {
        return $this->latestVersion;
    }
}
