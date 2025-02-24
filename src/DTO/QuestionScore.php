<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class QuestionScore
{
    public function __construct(        
        public string $examQuestion,
        public int $maxScore,
        public string $questionYield,
        public float $questionAverageScore,
        public float $pValue
    ) {}
}
