<?php

namespace App\External;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class HerokuApi
{
    public function __construct(
        private HttpClientInterface $herokuClient
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function updateFormationQuantity(string $appName, string $formationType, int $quantity): array
    {
        $res = $this->herokuClient->request(
            Request::METHOD_PATCH,
            'apps/' . $appName . '/formation/' . $formationType,
            [
                'json' => [
                    'quantity' => $quantity,
                ],
            ]
        );

        return json_decode($res->getContent(), true);
    }
}
