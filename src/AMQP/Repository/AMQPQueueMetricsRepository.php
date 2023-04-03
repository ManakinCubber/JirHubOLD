<?php

namespace App\AMQP\Repository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class AMQPQueueMetricsRepository
{
    public function __construct(
        private HttpClientInterface $amqpClient,
        private string $vhost
    ) {
    }

    public function getQueuesMetrics(): array
    {
        $response = $this->amqpClient->request(Request::METHOD_GET, '/api/queues/'.$this->vhost);

        return $response->toArray();
    }
}
