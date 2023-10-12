<?php

namespace Tests\Transport;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Jviapc\Reqres\Transport\Client;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class ClientTest extends TestCase
{
    public function testCreate(): void
    {
        $url = 'https://example.com';
        $name = 'John';
        $job = 'Doe';

        $httpClient = Mockery::mock(ClientInterface::class);
        $httpClient
            ->expects('sendRequest')
            ->withArgs(
                function (RequestInterface $request) use ($url, $name, $job) {
                    $this->assertHeader($request, 'Content-Type', 'application/json');
                    $this->assertEquals('POST', $request->getMethod());
                    $this->assertEquals($url . '/users', (string) $request->getUri());

                    /** @noinspection JsonEncodingApiUsageInspection */
                    $contents = json_decode($request->getBody()->getContents(), true);

                    $this->assertEquals(
                        [
                            'name' => $name,
                            'job' => $job,
                        ],
                        $contents
                    );

                    return true;
                }
            )
            ->andReturns(Mockery::mock(ResponseInterface::class));

        $this->makeClient($url, $httpClient)->create($name, $job);
    }

    /**
     * @throws Throwable
     */
    public function testListUsers(): void
    {
        $url = 'https://example.com';
        $page = random_int(0, 100);
        $limit = random_int(100, 100);

        $httpClient = Mockery::mock(ClientInterface::class);
        $httpClient
            ->expects('sendRequest')
            ->withArgs(
                function (RequestInterface $request) use ($url, $page, $limit) {
                    $this->assertHeader($request, 'Accept', 'application/json');
                    $this->assertEquals('GET', $request->getMethod());
                    $this->assertEquals("{$url}/users?page={$page}&per_page={$limit}", (string) $request->getUri());

                    return true;
                }
            )
            ->andReturns(Mockery::mock(ResponseInterface::class));

        $this->makeClient($url, $httpClient)->listUsers($page, $limit);
    }

    /**
     * @throws Throwable
     */
    public function testSingleUser(): void
    {
        $url = 'https://example.com';
        $id = random_int(0, 100);

        $httpClient = Mockery::mock(ClientInterface::class);
        $httpClient
            ->expects('sendRequest')
            ->withArgs(
                function (RequestInterface $request) use ($url, $id) {
                    $this->assertHeader($request, 'Accept', 'application/json');
                    $this->assertEquals('GET', $request->getMethod());
                    $this->assertEquals("{$url}/users/{$id}", (string) $request->getUri());

                    return true;
                }
            )
            ->andReturns(Mockery::mock(ResponseInterface::class));

        $this->makeClient($url, $httpClient)->singleUser($id);
    }

    private function assertHeader(RequestInterface $request, string $header, ?string $value = null): void
    {
        $this->assertTrue($request->hasHeader($header));

        if ($value !== null) {
            $this->assertEquals($value, $request->getHeader($header)[0] ?? '');
        }
    }

    private function makeClient(string $url, $httpClient): Client
    {
        $requestFactory = new class () implements \Psr\Http\Message\RequestFactoryInterface
        {
            public function createRequest(string $method, $uri): RequestInterface
            {
                return new Request(strtoupper($method), $uri);
            }
        };

        $streamFactory = new class () implements \Psr\Http\Message\StreamFactoryInterface
        {
            public function createStream(string $content = ''): \Psr\Http\Message\StreamInterface
            {
                return Utils::streamFor($content);
            }

            public function createStreamFromFile(
                string $filename,
                string $mode = 'r'
            ): \Psr\Http\Message\StreamInterface {
                throw new RuntimeException('Not implemented');
            }

            public function createStreamFromResource($resource): \Psr\Http\Message\StreamInterface
            {
                throw new RuntimeException('Not implemented');
            }
        };

        return new \Jviapc\Reqres\Transport\Client($url, $httpClient, $requestFactory, $streamFactory);
    }
}
