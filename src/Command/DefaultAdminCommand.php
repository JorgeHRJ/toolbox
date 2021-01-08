<?php

namespace App\Command;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DefaultAdminCommand extends Command
{
    private $userService;
    private $encoder;

    public function __construct(UserService $userService, UserPasswordEncoderInterface $encoder)
    {
        $this->userService = $userService;
        $this->encoder = $encoder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('toolbox:user:default-admin')
            ->setDescription('Create default admin user');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $user = new User();
            $user->setEmail('admin@toolbox.loc');
            $user->setStatus(User::ENABLED_STATUS);
            $user->setRoles([User::ROLE_ADMIN]);
            $user->setPassword($this->encoder->encodePassword($user, '0e842b75e5f8473161ee799ef5a129fd'));

            $this->userService->create($user);

            $io->success('Default admin created!');

            return 0;
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return -1;
        }
    }
}
