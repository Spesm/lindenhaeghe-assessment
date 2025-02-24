<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class ExamData
{
    public function __construct(        
        public array $examQuestions,
        public array $questionMaxScores,
        public array $studentScores,
        public int $maxTotalScore,
    ) {}
}