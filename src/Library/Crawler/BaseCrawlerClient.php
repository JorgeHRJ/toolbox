<?php

namespace App\Library\Crawler;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class BaseCrawlerClient
{
    protected HttpClientInterface $client;
    protected LoggerInterface $logger;

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
            throw new \Exception(
                sprintf('There was an error in the request made. URL %s, status code %s', $url, $statusCode)
            );
        }

        try {
            return $response->getContent();
        } catch (HttpExceptionInterface | TransportExceptionInterface $e) {
            throw new \Exception(sprintf('Error when trying to get content from response: %s', $e->getMessage()));
        }
    }
}
