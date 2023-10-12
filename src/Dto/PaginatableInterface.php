<?php

namespace Jviapc\Reqres\Dto;

interface PaginatableInterface
{
    public function hasNext(): bool;

    public function goNext(): static;
}
