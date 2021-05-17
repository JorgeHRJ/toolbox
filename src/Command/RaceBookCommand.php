<?php

namespace App\Command;

use App\Entity\User;
use App\Service\RaceBook\RaceBookService;
use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RaceBookCommand extends Command
{
    const COMMAND_NAME = 'toolbox:race:process';

    private RaceBookService $raceBookService;
    private UserService $userService;

    public function __construct(RaceBookService $raceBookService, UserService $userService)
    {
        $this->raceBookService = $raceBookService;
        $this->userService = $userService;
        parent::__construct(self::COMMAND_NAME);
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Process Race Book from URL')
            ->addArgument('userId', InputArgument::REQUIRED, 'User ID')
            ->addArgument('url', InputArgument::REQUIRED, 'URL')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $userId = (int) $input->getArgument('userId');
        $url = (string) $input->getArgument('url');

        $user = $this->userService->get(null, $userId);
        if (!$user instanceof User) {
            $style->error('Usuario no encontrado!');
            return self::FAILURE;
        }

        $this->raceBookService->process($user, $url);

        return self::SUCCESS;
    }
}
