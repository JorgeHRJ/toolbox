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
            ->addArgument('url', InputArgument::REQUIRED, 'URL')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $style->title('Race Book import process');

        $url = (string) $input->getArgument('url');

        $users = $this->userService->getByRoles([User::ROLE_RACEBOOK, User::ROLE_ADMIN]);
        foreach ($users as $user) {
            $this->raceBookService->process($user, $url);
        }

        $style->success('Finished!');
        return self::SUCCESS;
    }
}
