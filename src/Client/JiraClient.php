<?php

namespace App\Client;

use App\Exception\UnexpectedContentType;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JiraClient
{
    private string $baseUrl;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $username,
        private readonly string $pass,
        string $host,
        string $version = '3'
    ) {
        $this->baseUrl = $host.'/rest/api/'.$version;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws UnexpectedContentType
     */
    public function request(
        string $route,
        string $method = 'GET',
        array $queryParameters = [],
        array $requestContent = []
    ): ?array {
        $response = $this->httpClient->request(
            $method,
            $this->baseUrl.$route,
            [
                'auth_basic' => [$this->username, $this->pass],
                'query' => $queryParameters,
                'json' => $requestContent,
            ]
        );

        $content = $response->getContent();

        if (204 === $response->getStatusCode()) {
            return null;
        }

        if (0 !== mb_strpos($response->getHeaders()['content-type'][0], 'application/json')) {
            throw new UnexpectedContentType();
        }

        return json_decode($content, true);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws UnexpectedContentType
     */
    public function get(string $route, array $queryParameters = []): ?array
    {
        return $this->request($route, 'GET', $queryParameters);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws UnexpectedContentType
     */
    public function post(string $route, array $queryParameters = [], array $requestContent = []): ?array
    {
        return $this->request($route, 'POST', $queryParameters, $requestContent);
    }
}
