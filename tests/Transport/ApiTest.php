<?php

namespace Tests\Transport;

use Exception;
use Jviapc\Reqres\Transport\Api;
use Jviapc\Reqres\Transport\Client;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use UnexpectedValueException;

class ApiTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateSuccess(): void
    {
        $id = random_int(0, 100);
        $name = random_bytes(10);
        $job = random_bytes(10);
        $statusCode = 201;

        $response = self::stubResponse($statusCode, json_encode(['id' => $id]));

        $client = Mockery::mock(Client::class);

        $client
            ->expects('create')
            ->with($name, $job)
            ->andReturns($response);

        $actualId = $this->api($client)->create($name, $job);

        $this->assertEquals($id, $actualId);
    }

    /**
     * @throws Exception
     */
    #[DataProvider('listUsersDataProvider')]
    public function testListUsers(array $page1, array $page2): void
    {
        $limit = random_int(0, 100);
        $statusCode = 200;

        $client = Mockery::mock(Client::class);
        $client
            ->expects('listUsers')
            ->with(1, $limit)
            ->andReturn($this->stubResponse($statusCode, json_encode($page1)));

        $client
            ->expects('listUsers')
            ->with(2, $limit)
            ->andReturn($this->stubResponse($statusCode, json_encode($page2)));

        $userCollection = $this->api($client)->listUsers(1, $limit);

        // First page
        $this->assertTrue($userCollection->hasNext());
        $userDto = $userCollection->current();
        $userResponse = $page1['data'][0];

        $this->assertEquals($userResponse['id'], $userDto->id);
        $this->assertEquals($userResponse['email'], $userDto->email);
        $this->assertEquals($userResponse['first_name'], $userDto->firstName);
        $this->assertEquals($userResponse['last_name'], $userDto->lastName);
        $this->assertEquals($userResponse['avatar'], $userDto->avatar);

        // Second page
        $userCollection = $userCollection->goNext();

        $this->assertFalse($userCollection->hasNext());
        $userDto = $userCollection->current();
        $userResponse = $page2['data'][0];

        $this->assertEquals($userResponse['id'], $userDto->id);
        $this->assertEquals($userResponse['email'], $userDto->email);
        $this->assertEquals($userResponse['first_name'], $userDto->firstName);
        $this->assertEquals($userResponse['last_name'], $userDto->lastName);
        $this->assertEquals($userResponse['avatar'], $userDto->avatar);
    }

    #[DataProvider('singleUserDataProvider')]
    public function testSingleUser(array $userResponse): void
    {
        $userId = $userResponse['id'];
        $statusCode = 200;

        $client = Mockery::mock(Client::class);
        $client
            ->expects('singleUser')
            ->with($userId)
            ->andReturns(self::stubResponse(
                $statusCode,
                json_encode(['data' => $userResponse])
            ));

        $userDto = $this->api($client)->singleUser($userId);

        $this->assertEquals($userResponse['id'], $userDto->id);
        $this->assertEquals($userResponse['email'], $userDto->email);
        $this->assertEquals($userResponse['first_name'], $userDto->firstName);
        $this->assertEquals($userResponse['last_name'], $userDto->lastName);
        $this->assertEquals($userResponse['avatar'], $userDto->avatar);
    }

    /**
     * @throws Exception
     */
    #[DataProvider('createFailureDataProvider')]
    public function testCreateFailure($clientReturn): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('create')
            ->andReturnUsing($clientReturn);

        $this->expectException(UnexpectedValueException::class);

        $this->api($client)->create('', '');
    }

    /**
     * @throws Exception
     */
    #[DataProvider('commonFailureDataProvider')]
    public function testListUsersFailure($clientReturn): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('listUsers')
            ->andReturnUsing($clientReturn);

        $this->expectException(UnexpectedValueException::class);

        $this->api($client)->listUsers(1, 1);
    }

    /**
     * @throws Exception
     */
    #[DataProvider('commonFailureDataProvider')]
    public function testSingleUserFailure($clientReturn): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('singleUser')
            ->andReturnUsing($clientReturn);

        $this->expectException(UnexpectedValueException::class);

        $this->api($client)->singleUser(1);
    }

    public static function singleUserDataProvider(): array
    {
        return [
            'valid user' => [
                [
                    'id' => random_int(1, 100),
                    'email' => 'george.bluth@reqres.in',
                    'first_name' => 'George',
                    'last_name' => 'Bluth',
                    'avatar' => 'some url',
                ],
            ],
        ];
    }

    public static function listUsersDataProvider(): array
    {
        $users = [
            [
                'id' => random_int(1, 100),
                'email' => 'george.bluth@reqres.in',
                'first_name' => 'George',
                'last_name' => 'Bluth',
                'avatar' => 'some url',
            ],
            [
                'id' => random_int(1, 100),
                'email' => 'j.d@reqres.in',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'avatar' => 'some url',
            ],
        ];

        return [
            'multiple users' => [
                [
                    'page' => 1,
                    'total_pages' => 2,
                    'data' => $users,
                ],
                [
                    'page' => 2,
                    'total_pages' => 2,
                    'data' => array_reverse($users),
                ],
            ],
        ];
    }

    public static function commonFailureDataProvider(): array
    {
        return [
            'client error' => [
                static fn() => throw new class () extends Exception implements ClientExceptionInterface {},
            ],
            'broken json' => [
                static fn() => self::stubResponse(201, '{df.4.d'),
            ],
            'unexpected status' => [
                static fn() => self::stubResponse(random_int(100, 199), ''),
            ],
        ];
    }

    public static function createFailureDataProvider(): array
    {
        return [
            ...self::commonFailureDataProvider(),
            'unexpected status' => [
                static fn() => self::stubResponse(random_int(100, 200), ''),
            ],
        ];
    }

    private static function stubResponse(int $status, string $content)
    {
        $responseMock = Mockery::mock(ResponseInterface::class);
        $streamMock = Mockery::mock(StreamInterface::class);

        $responseMock
            ->allows('getStatusCode')
            ->andReturns($status);

        $responseMock
            ->allows('getBody')
            ->andReturns($streamMock);

        $streamMock
            ->allows('getContents')
            ->andReturns($content);

        return $responseMock;
    }

    protected function api(MockInterface|Client $client): Api
    {
        return new Api($client);
    }
}
