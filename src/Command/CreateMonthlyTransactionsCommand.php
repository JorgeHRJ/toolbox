<?php

namespace App\Command;

use App\Entity\TransactionMonth;
use App\Service\TransactionCategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMonthlyTransactionsCommand extends Command
{
    const BATCH_SIZE = 50;

    private $categoryService;
    private $entityManager;
    private $logger;

    public function __construct(
        TransactionCategoryService $categoryService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->categoryService = $categoryService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('toolbox:transaction:monthly')
            ->setDescription('Create monthly periodic transactions');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new \DateTime();
        $newMonthStr = $now->format('m');
        $newYearStr = $now->format('Y');

        $previousMonth = new \DateTime('-1 month');
        $previousMonthStr = $previousMonth->format('m');
        $previousYearStr = $previousMonth->format('Y');

        $categories = $this->categoryService->getMonthlyFromPreviousMonth($previousYearStr, $previousMonthStr);

        $counter = 0;
        foreach ($categories as $category) {
            $lastMonth = $category->getMonths()[0];

            $month = new TransactionMonth();
            $month->setMonth((int) $newMonthStr);
            $month->setYear((int) $newYearStr);
            $month->setValue('0.0');
            $month->setExpected($lastMonth->getExpected());
            $month->setCategory($category);

            $this->entityManager->persist($month);
            $this->logger->info(
                sprintf('Persisted for month %s for category %s', $newMonthStr, $category->getTitle())
            );

            $counter++;

            if ($counter === self::BATCH_SIZE) {
                $this->entityManager->flush();
                $counter = 0;
            }
        }

        $this->entityManager->flush();
        $this->logger->info('Finished CreateMonthlyTransactionsCommand successfully!');

        return Command::SUCCESS;
    }
}
