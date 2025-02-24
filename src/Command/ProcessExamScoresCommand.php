<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ExamScoreSheetParser;
use App\Service\ExamDataProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsCommand(
    name: 'app:process-exam-scores',
    description: 'Process and analyze exam results.'
)]
class ProcessExamScoresCommand extends Command
{
    public function __construct(
        private readonly ExamDataProcessor $processor,
        private readonly ExamScoreSheetParser $parser,
        private readonly NormalizerInterface $serializer
        )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('spreadsheet', InputArgument::REQUIRED, 'Path to spreadsheet file containing exam scores');
        $this->addOption('results', 'r', InputOption::VALUE_NONE, 'Calculate the exam results');
        $this->addOption('analysis', 'a', InputOption::VALUE_NONE, 'Analyze the exam questions');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('spreadsheet');

        if (file_exists($filePath) === false) {
            throw new FileNotFoundException(sprintf('File %s not found', $filePath));
        }

        $io = new SymfonyStyle($input, $output);

        try {
            $examData = $this->parser->parseSheet($filePath);
        } catch(\Exception $e) {   
            $io->error(sprintf('Failed to process the input file "%s": %s', $filePath, $e->getMessage()));

            return Command::FAILURE;
        }


        $io->title(sprintf('Processing input file "%s"', $filePath));

        if ($input->getOption('results') || $input->getOption('analysis') === false) {

            $outputData = $this->processor->calculateResults($examData);

            $io->table([
                'Student', 
                'Score', 
                'Grade', 
                'Result'
            ],
                $this->serializer->normalize($outputData)
            );
        }

        if ($input->getOption('analysis')) {

            $outputData = $this->processor->analyzeQuestions($examData);

            $io->table([
                'Question', 
                'Max Score', 
                'Question Yield', 
                'Average Score', 
                'P\'-value'
            ],
                $this->serializer->normalize($outputData)
            );
        }

        $io->success(sprintf('Successfully processed file "%s"', $filePath));

        return Command::SUCCESS;
    }
}