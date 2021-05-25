<?php

namespace App\Service;

use App\Entity\IrrigationProcess;
use App\Repository\IrrigationProcessRepository;
use Doctrine\ORM\EntityManagerInterface;

class IrrigationProcessService
{
    private EntityManagerInterface $entityManager;
    private IrrigationProcessRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(IrrigationProcess::class);
    }

    public function create(): IrrigationProcess
    {
        $process = new IrrigationProcess();
        $process
            ->setDate(new \DateTime())
            ->setErrors([]);

        $this->entityManager->persist($process);
        $this->entityManager->flush();

        return $process;
    }

    public function update(IrrigationProcess $process, array $errors): void
    {
        $process->setErrors($errors);

        $this->entityManager->flush();
    }
}
