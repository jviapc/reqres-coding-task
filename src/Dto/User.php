<?php

declare(strict_types=1);

namespace Jviapc\Reqres\Dto;

use JsonSerializable;

readonly class User implements JsonSerializable
{
    public function __construct(
        public int $id,
        public string $email,
        public string $firstName,
        public string $lastName,
        public string $avatar,
    ) {}

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'avatar' => $this->avatar,
        ];
    }
}
