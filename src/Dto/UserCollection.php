<?php

declare(strict_types=1);

namespace Jviapc\Reqres\Dto;

use ArrayIterator;
use Closure;
use Iterator;

readonly class UserCollection implements Iterator, PaginatableInterface
{
    private ArrayIterator $users;

    public function __construct(
        private bool $hasNext,
        private Closure $loadNext,
        User ...$users
    ) {
        $this->users = new ArrayIterator($users);
    }

    public function current(): User
    {
        return $this->users->current();
    }

    public function next(): void
    {
        $this->users->next();
    }

    public function key(): int
    {
        return $this->users->key();
    }

    public function valid(): bool
    {
        return $this->users->valid();
    }

    public function rewind(): void
    {
        $this->users->rewind();
    }

    public function hasNext(): bool
    {
        return $this->hasNext;
    }

    public function goNext(): static
    {
        return ($this->loadNext)();
    }
}
