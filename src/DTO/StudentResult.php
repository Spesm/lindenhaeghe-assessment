<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class StudentResult
{
    public function __construct(        
        public string $studentId,
        public float $studentScore,
        public float $studentGrade,
        public string $studentHasPassed,
    ) {}
}