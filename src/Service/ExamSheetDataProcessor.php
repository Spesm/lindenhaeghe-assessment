<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExamSheetDataProcessor
{
    private int $requiredScorePercent = 70;
    private int $smallestScorePercent = 20;
    private array $sheetData;
    private array $examQuestions; 
    private array $questionMaxScores;
    private int $maxTotalScore;

    public function processSpreadsheet(string $filePath): ExamSheetDataProcessor
    {
        $sheetFile = IOFactory::load($filePath);

        $this->sheetData = $sheetFile->getActiveSheet()->toArray();
        $this->examQuestions = array_slice(array_shift($this->sheetData), 1);
        $this->questionMaxScores = array_slice(array_shift($this->sheetData), 1);
        $this->maxTotalScore = array_sum(array_filter($this->questionMaxScores, 'is_numeric'));

        return $this;
    }

    public function calculateResults(): array
    {
        $caesuraThreshold = $this->maxTotalScore * $this->requiredScorePercent / 100;
        $caesuraMinimum = $this->maxTotalScore * $this->smallestScorePercent / 100;

        $examResults = [];

        foreach ($this->sheetData as $studentRow) {

            $studentId = array_shift($studentRow);
            $studentScore = array_sum(array_filter($studentRow, 'is_numeric'));

            $studentResult = "failed";

            if ($studentScore <= $caesuraMinimum) {
                $studentGrade = 1.0;
            }

            if ($studentScore < $caesuraThreshold) {
                $studentGrade = round(1 + 4.5 * ($studentScore - $caesuraMinimum) / ($caesuraThreshold - $caesuraMinimum), 1);
            }

            if ($studentScore >= $caesuraThreshold) {
                $studentGrade = round(5.5 + 4.5 * ($studentScore - $caesuraThreshold) / ($this->maxTotalScore - $caesuraThreshold), 1);

                $studentResult = "passed";
            }

            $examResults[] = [$studentId, $studentScore, $studentGrade, $studentResult];
        }

        return $examResults;
    }

    public function analyzeQuestions(): array
    {
        $questionResults = [];

        for ($i = 0; $i < count($this->examQuestions); $i ++) {

            $questionTotalScore = array_sum(array_filter(array_column($this->sheetData, $i + 1), 'is_numeric'));
            $questionAverageScore = round($questionTotalScore / count($this->sheetData), 3);

            $questionMaxScore = $this->questionMaxScores[$i];
            $questionScoreYield = sprintf('%s / %s', $questionTotalScore, $questionMaxScore * count($this->sheetData));

            $pValue = round($questionAverageScore / $questionMaxScore, 3);

            $questionResults[] = [$this->examQuestions[$i], $questionMaxScore, $questionScoreYield, $questionAverageScore, $pValue];
        }

        return $questionResults;
    } 
}