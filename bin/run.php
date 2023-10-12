<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

require dirname(__DIR__) . '/vendor/autoload.php';

$httpClient = new class (new Client()) implements ClientInterface
{
    public function __construct(
        private readonly Client $guzzle
    ) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->guzzle->send($request);
        } catch (BadResponseException $e) {
            return $e->getResponse();
        } catch (Throwable $e) {
            throw new class ($e->getMessage(), $e->getCode(), $e) extends Exception implements ClientExceptionInterface {};
        }
    }
};

$requestFactory = new class () implements \Psr\Http\Message\RequestFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request(strtoupper($method), $uri);
    }
};

$streamFactory = new class () implements \Psr\Http\Message\StreamFactoryInterface
{
    public function createStream(string $content = ''): Psr\Http\Message\StreamInterface
    {
        return Utils::streamFor($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): Psr\Http\Message\StreamInterface
    {
        throw new RuntimeException('Not implemented');
    }

    public function createStreamFromResource($resource): Psr\Http\Message\StreamInterface
    {
        throw new RuntimeException('Not implemented');
    }
};

$client = new \Jviapc\Reqres\Transport\Client(
    'https://reqres.in/api',
    $httpClient,
    $requestFactory,
    $streamFactory
);

$api = new \Jviapc\Reqres\Transport\Api($client);

echo 'Single user downloading', PHP_EOL;
var_dump(json_encode($api->singleUser(3)));

echo 'Create user', PHP_EOL;
var_dump($api->create('John', 'Doe'));

echo 'List users, from second page, limiting to two', PHP_EOL;
$userCollection = $api->listUsers(2, 2);

while ($userCollection->hasNext()) {
    foreach ($userCollection as $user) {
        var_dump(json_encode($user));
    }

    echo 'Next page', PHP_EOL;
    $userCollection = $userCollection->goNext();
}
