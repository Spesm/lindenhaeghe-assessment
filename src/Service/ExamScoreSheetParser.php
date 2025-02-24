<?php

namespace App\Service;

use App\DTO\ExamData;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExamScoreSheetParser
{
    public function parseSheet(string $filePath): ExamData
    {
        $sheetFile = IOFactory::load($filePath);
        $sheetData = $sheetFile->getActiveSheet()->toArray();

        if (empty($sheetData) || count($sheetData) < 2) {
            throw new \InvalidArgumentException('The input file does not have the required structure.');
        }

        $examQuestions = array_slice(array_shift($sheetData), 1);
        $questionMaxScores = array_slice(array_shift($sheetData), 1);
        $maxTotalScore = array_sum(array_filter($questionMaxScores, 'is_numeric'));

        return new ExamData($examQuestions, $questionMaxScores, $sheetData, $maxTotalScore);
    }
}