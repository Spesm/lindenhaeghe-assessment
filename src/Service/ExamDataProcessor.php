<?php

declare(strict_types=1);

namespace App\Service;

use App\Collection\QuestionScores;
use App\Collection\StudentResults;
use App\DTO\QuestionScore;
use App\DTO\StudentResult;

class ExamDataProcessor
{
    private int $requiredScorePercent = 70;
    private int $smallestScorePercent = 20;

    public function calculateResults($examData): StudentResults
    {
        $caesuraThreshold = $examData->maxTotalScore * $this->requiredScorePercent / 100;
        $caesuraMinimum = $examData->maxTotalScore * $this->smallestScorePercent / 100;

        $examResults = [];

        foreach ($examData->studentScores as $studentRow) {

            $studentId = array_shift($studentRow);
            $studentScore = array_sum(array_filter($studentRow, 'is_numeric'));

            $studentGrade = match (true) {
                $studentScore <= $caesuraMinimum => 1.0,
                $studentScore < $caesuraThreshold => round(1 + 4.5 * ($studentScore - $caesuraMinimum) / ($caesuraThreshold - $caesuraMinimum), 1),
                $studentScore >= $caesuraThreshold => round(5.5 + 4.5 * ($studentScore - $caesuraThreshold) / ($examData->maxTotalScore - $caesuraThreshold), 1),
            };

            $failedOrPassed = $studentGrade < 5.5 ? 'failed' : 'passed';

            $examResults[] = new StudentResult($studentId, $studentScore, $studentGrade, $failedOrPassed);
        }

        return new StudentResults(...$examResults);
    }

    public function analyzeQuestions($examData): QuestionScores
    {
        $questionScores = [];

        for ($i = 0; $i < count($examData->examQuestions); $i ++) {

            $questionTotalScore = array_sum(array_filter(array_column($examData->studentScores, $i + 1), 'is_numeric'));
            $questionAverageScore = round($questionTotalScore / count($examData->studentScores), 3);

            $questionMaxScore = intval($examData->questionMaxScores[$i]);
            $questionScoreYield = sprintf('%s / %s', $questionTotalScore, $questionMaxScore * count($examData->studentScores));

            $pValue = round($questionAverageScore / $questionMaxScore, 3);

            $questionScores[] = new QuestionScore($examData->examQuestions[$i], $questionMaxScore, $questionScoreYield, $questionAverageScore, $pValue);
        }

        return new QuestionScores(...$questionScores);
    } 
}