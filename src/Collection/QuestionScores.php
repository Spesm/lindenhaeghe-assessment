<?php

declare(strict_types=1);

namespace App\Collection;

use App\DTO\QuestionScore;

final readonly class QuestionScores implements \IteratorAggregate, \Countable
{
    private array $score;

    public function __construct(QuestionScore ...$score)
    {
        $this->score = $score;
    }

    public function getAll(): array
    {
        return $this->score;
    }

    public function count(): int
    {
        return count($this->score);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->score);
    }
}