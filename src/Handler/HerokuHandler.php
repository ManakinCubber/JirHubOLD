<?php

namespace App\Handler;

use App\External\HerokuApi;
use GuzzleHttp\Exception\GuzzleException;

class HerokuHandler
{
    /** @var HerokuApi */
    private $herokuApi;

    public function __construct(HerokuApi $herokuApi)
    {
        $this->herokuApi = $herokuApi;
    }

    /**
     * @throws GuzzleException
     */
    public function updateDynoQuantity(array $appNames, array $dynoTypes, int $quantity)
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
