<?php

declare(strict_types=1);

namespace App\Collection;

use App\DTO\StudentResult;

final readonly class StudentResults implements \IteratorAggregate, \Countable
{
    private array $results;

    public function __construct(StudentResult ...$results)
    {
        $this->results = $results;
    }

    public function getAll(): array
    {
        return $this->results;
    }

    public function count(): int
    {
        return count($this->results);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->results);
    }
}