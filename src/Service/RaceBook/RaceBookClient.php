<?php

namespace App\Service\RaceBook;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RaceBookClient
{
    private HttpClientInterface $client;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function call(string $url): string
    {
        $response = $this->client->request('GET', $url);
        $statusCode = $response->getStatusCode();

        $this->logger->info(sprintf('Request made to %s. Response status: %d', $url, $statusCode));

        if ($statusCode !== Response::HTTP_OK) {
            throw new \Exception('No se ha podido obtener información sobre la URL indicada.');
        }

        try {
            return $response->getContent();
        } catch (HttpExceptionInterface | TransportExceptionInterface $e) {
            $this->logger->error(sprintf('Error when trying to get content from response: %s', $e->getMessage()));
            throw new \Exception('No se ha podido obtener información sobre la URL indicada.');
        }
    }
}
