<?php

namespace Jviapc\Reqres\Transport;

use Jviapc\Reqres\Dto\User;
use Jviapc\Reqres\Dto\UserCollection;

interface ApiInterface
{
    public function singleUser(int $userId): User;

    public function listUsers(int $page, int $limit): UserCollection;

    public function create(string $name, string $job): int;
}
