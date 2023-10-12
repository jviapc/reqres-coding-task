<?php

declare(strict_types=1);

namespace Jviapc\Reqres\Transport;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Client
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {}

    /**
     * @throws ClientExceptionInterface
     */
    public function singleUser(int $userId): ResponseInterface
    {
        $request = $this->requestFactory
            ->createRequest(
                'GET',
                "{$this->baseUrl}/users/{$userId}"
            )
            ->withHeader('Accept', 'application/json');

        return $this->client->sendRequest($request);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function listUsers(int $page, int $limit): ResponseInterface
    {
        $request = $this->requestFactory
            ->createRequest(
                'GET',
                "{$this->baseUrl}/users?page={$page}&per_page={$limit}",
            )
            ->withHeader('Accept', 'application/json');

        return $this->client->sendRequest($request);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function create(string $name, string $job): ResponseInterface
    {
        $request = $this->requestFactory
            ->createRequest(
                'POST',
                "{$this->baseUrl}/users",
            )
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                $this->streamFactory->createStream(
                    json_encode([
                        'name' => $name,
                        'job' => $job,
                    ])
                )
            );

        return $this->client->sendRequest($request);
    }
}
