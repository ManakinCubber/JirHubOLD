<?php

namespace App\Handler;

use App\External\HerokuApi;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class HerokuHandler
{
    public function __construct(private HerokuApi $herokuApi)
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function updateDynoQuantity(array $appNames, array $dynoTypes, int $quantity): void
    {
        foreach ($appNames as $appName) {
            if (!$this->isAppManageable($appName)) {
                continue;
            }

            foreach ($dynoTypes as $dynoType) {
                $this->herokuApi->updateFormationQuantity($appName, $dynoType, $quantity);
            }
        }
    }

    private function isAppManageable(string $appName): bool
    {
        if ('chronos' !== mb_substr($appName, 0, 7)) {
            return false;
        }

        if (false !== mb_strpos($appName, 'development')) {
            return false;
        }

        if (false !== mb_strpos($appName, 'staging')) {
            return false;
        }

        if (false !== mb_strpos($appName, 'production')) {
            return false;
        }

        return true;
    }
}
