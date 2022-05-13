<?php

namespace App\AMQP\Repository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AMQPQueueMetricsRepository
{
    private HttpClientInterface $httpClient;
    private string $vhost;

    public function __construct(HttpClientInterface $amqpClient, string $vhost)
    {
        $this->httpClient = $amqpClient;
        $this->vhost = $vhost;
    }

    public function getQueuesMetrics(): string
    {
        $response = $this->httpClient->request(Request::METHOD_GET, '/api/queues/'.$this->vhost);
        return $response->getContent();
    }
}