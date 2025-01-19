# Exam Scores Processing Application

I have developed this Symfony CLI application as part of an assessment for a PHP Developer role at Lindenhaeghe. The project processes exam scores from a spreadsheet, calculates student's grades using a caesura and provides analysis on the question performance.

---

## Features

- **Process Exam Results**: Calculate student scores, grades, and determine pass/fail status based on configurable thresholds.
- **Analyze Exam Questions**: Evaluate the performance of individual questions, including metrics such as the average score and P'-value.
- **Extensible Design**: The application is designed for future enhancements, such as calculating item-total correlation coefficients (`r(it)` values), which is not yet implemented at this point.

---

## Requirements

- PHP 8.2 or higher
- Symfony 6 or higher
- Composer
- The [Symfony CLI](https://symfony.com/download) installed for running the development server.

---

## Installation

### 1. Clone the Repository
```bash
git clone https://github.com/Spesm/lindenhaeghe-assessment.git
cd exam-scores-processor
```
### 2. Set Up Environment
- Copy the `.env.example` file to `.env`:
```bash
cp .env.example .env
```
### 3. Install Dependencies
```bash
composer install
```
---

## Usage

### 1. Process Exam Data
Run the custom Symfony command:
```bash
php bin/console app:process-exam-scores <path-to-spreadsheet>
```

### 2. Options
- **Calculate Results**: Add the `--results` or `-r` option to calculate and display student results.
- **Analyze Questions**: Add the `--analysis` or `-a` option to analyze exam question performance.

For convenience, the test file examdata.xlsx is included in the project's root directory, so you can run:
```bash
php bin/console app:process-exam-scores examdata.xlsx --results --analysis
```

---

## File Structure

- `src/Command/ProcessExamScoresCommand.php`:
  Implements the Symfony console command for processing exam data.

- `src/Service/ExamSheetDataProcessor.php`:
  Contains the logic for reading spreadsheet data, calculating results, and analyzing questions.

---

## Improvements and Future Work

- Add more robust validation for spreadsheet input data.
- Extend functionality to support additional file formats (e.g., CSV).
- Implement a web interface for uploading files and viewing results.
- Add unit and integration tests for greater reliability.

---

## About

This project was developed as an assessment for a role at **Lindenhaeghe** to showcase proficiency with PHP, Symfony, and data processing workflows.

---

## License

This project is licensed under the [MIT License](LICENSE).
