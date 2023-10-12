<?php

declare(strict_types=1);

namespace Jviapc\Reqres\Transport;

use JsonException;
use Jviapc\Reqres\Dto\User;
use Jviapc\Reqres\Dto\UserCollection;
use Psr\Http\Client\ClientExceptionInterface;
use UnexpectedValueException;

readonly class Api implements ApiInterface
{
    public function __construct(
        private Client $client,
    ) {}

    public function singleUser(int $userId): User
    {
        try {
            $response = $this->client->singleUser($userId);
        } catch (ClientExceptionInterface $e) {
            throw new UnexpectedValueException('ReqRes API client error', $e->getCode(), $e);
        }

        if ($response->getStatusCode() !== 200) {
            throw new UnexpectedValueException(
                sprintf(
                    'Invalid ReqRes API response status code [%s], expected 200',
                    $response->getStatusCode()
                )
            );
        }

        try {
            $responseData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new UnexpectedValueException('Invalid ReqRes API response json', $e->getCode(), $e);
        }

        return new User(
            id: (int) $responseData['data']['id'],
            email: $responseData['data']['email'],
            firstName: $responseData['data']['first_name'],
            lastName: $responseData['data']['last_name'],
            avatar: $responseData['data']['avatar'],
        );
    }

    public function listUsers(int $page, int $limit): UserCollection
    {
        if ($page < 0) {
            $page = 0;
        }

        if ($limit < 0) {
            $limit = 0;
        }

        try {
            $response = $this->client->listUsers($page, $limit);
        } catch (ClientExceptionInterface $e) {
            throw new UnexpectedValueException('ReqRes API client error', $e->getCode(), $e);
        }

        if ($response->getStatusCode() !== 200) {
            throw new UnexpectedValueException(
                sprintf(
                    'Invalid ReqRes API response status code [%s], expected 200',
                    $response->getStatusCode()
                )
            );
        }

        try {
            $responseData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new UnexpectedValueException('Invalid ReqRes API response json', $e->getCode(), $e);
        }

        $hasNext = $responseData['page'] < $responseData['total_pages'];

        return new UserCollection(
            $hasNext,
            fn() => $this->listUsers($page + 1, $limit),
            ...array_map(
                static fn($v) => new User(
                    id: (int) $v['id'],
                    email: $v['email'],
                    firstName: $v['first_name'],
                    lastName: $v['last_name'],
                    avatar: $v['avatar'],
                ),
                $responseData['data']
            )
        );
    }

    public function create(string $name, string $job): int
    {
        try {
            $response = $this->client->create($name, $job);
        } catch (ClientExceptionInterface $e) {
            throw new UnexpectedValueException('ReqRes API client error', $e->getCode(), $e);
        }

        if ($response->getStatusCode() !== 201) {
            throw new UnexpectedValueException(
                sprintf(
                    'Invalid ReqRes API response status code [%s], expected 201',
                    $response->getStatusCode()
                )
            );
        }

        try {
            $responseData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new UnexpectedValueException('Invalid ReqRes API response json', $e->getCode(), $e);
        }

        return (int) $responseData['id'];
    }
}
