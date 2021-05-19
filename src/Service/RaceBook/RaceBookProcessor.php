<?php

namespace App\Service\RaceBook;

use App\Entity\Classic;
use App\Entity\Cyclist;
use App\Entity\CyclistRace;
use App\Entity\GrandTour;
use App\Entity\Race;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Win;
use App\Service\CyclistRaceService;
use App\Service\CyclistService;
use App\Service\RaceService;
use App\Service\TeamService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class RaceBookProcessor
{
    private RaceService $raceService;
    private TeamService $teamService;
    private CyclistService $cyclistService;
    private CyclistRaceService $cyclistRaceService;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private SluggerInterface $slugger;

    public function __construct(
        RaceService $raceService,
        TeamService $teamService,
        CyclistService $cyclistService,
        CyclistRaceService $cyclistRaceService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        SluggerInterface $slugger
    ) {
        $this->raceService = $raceService;
        $this->teamService = $teamService;
        $this->cyclistService = $cyclistService;
        $this->cyclistRaceService = $cyclistRaceService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->slugger = $slugger;
    }

    public function processTeamsData(User $user, Race $race, array $teamsData): void
    {
        foreach ($teamsData as $teamData) {
            $team = $this->processTeamData($teamData['team']);
            $cyclistsData = $teamData['cyclists'];

            foreach ($cyclistsData as $cyclistData) {
                $cyclist = $this->processCyclistData($cyclistData, $team);
                $this->processCyclistRace($user, $race, $cyclist, (int) $cyclistData['dorsal']);
            }
        }
    }

    public function processRaceData(array $raceData): Race
    {
        $race = $this->raceService->getByName($raceData['name']);
        if ($race instanceof Race) {
            return $race;
        }

        $year = $raceData['start_date']->format('Y');

        $race = new Race();
        $race
            ->setName($raceData['name'])
            ->setSlug($this->slugger->slug(strtolower(sprintf('%s %s', $raceData['name'], $year))))
            ->setYear($year)
            ->setCategory($raceData['category'])
            ->setUciTour($raceData['uci_tour'])
            ->setStartlistUrl($raceData['url'])
            ->setStartDate($raceData['start_date'])
            ->setEndDate($raceData['end_date']);

        /** @var Race $race */
        $race = $this->raceService->create($race);

        $this->logger->info(sprintf('Race %s processed!', $race->getName()));

        return $race;
    }

    private function processTeamData(string $name): Team
    {
        $team = $this->teamService->getByName($name);
        if ($team instanceof Team) {
            return $team;
        }

        $team = new Team();
        $team
            ->setName($name)
            ->setSlug($this->slugger->slug(strtolower($name)));

        /** @var Team $team */
        $team = $this->teamService->create($team);

        $this->logger->info(sprintf('Team %s processed!', $team->getName()));

        return $team;
    }

    private function processCyclistData(array $data, Team $team): Cyclist
    {
        $needPersist = false;
        $cyclist = $this->cyclistService->getByName($data['name']);
        if (!$cyclist instanceof Cyclist) {
            $cyclist = new Cyclist();
            $needPersist = true;
        }

        $cyclist
            ->setName($data['name'])
            ->setSlug($this->slugger->slug(strtolower($data['name'])))
            ->setTeam($team)
            ->setBirthDate($data['birthdate'])
            ->setNationality($data['nationality'])
            ->setWeight($data['weight'])
            ->setHeight($data['height'])
            ->setLocation($data['location']);

        /** @var Cyclist $cyclist */
        $cyclist = $needPersist
            ? $this->cyclistService->create($cyclist)
            : $this->cyclistService->edit($cyclist);

        $this->logger->info(sprintf('Cyclist %s processed!', $cyclist->getName()));

        $connection = $this->entityManager->getConnection();

        foreach (['win', 'grandtour', 'classic'] as $table) {
            $statement = $connection->prepare(sprintf('DELETE FROM %1$s WHERE %1$s_cyclist = :cyclistId', $table));
            $statement->bindValue('cyclistId', $cyclist->getId());
            $statement->execute();
        }

        if (isset($data['wins'])) {
            $this->processCyclistWins($cyclist, $data['wins']);
        }

        if (isset($data['grand_tours'])) {
            $this->processCyclistGrandTours($cyclist, $data['grand_tours']);
        }

        if (isset($data['classics'])) {
            $this->processCyclistClassics($cyclist, $data['classics']);
        }

        $this->logger->info(sprintf('Races data for cyclist %s processed!', $cyclist->getName()));

        return $cyclist;
    }

    private function processCyclistWins(Cyclist $cyclist, array $winsData): void
    {
        foreach ($winsData as $winData) {
            $win = new Win();
            $win
                ->setRace($winData['race'])
                ->setType($winData['type'])
                ->setClass($winData['class'])
                ->setDate($winData['date'])
                ->setCyclist($cyclist);

            $this->entityManager->persist($win);
        }
        $this->entityManager->flush();
    }

    private function processCyclistGrandTours(Cyclist $cyclist, array $grandToursData): void
    {
        foreach ($grandToursData as $grandTourData) {
            $grandTour = new GrandTour();
            $grandTour
                ->setSeason($grandTourData['season'])
                ->setName($grandTourData['grand_tour'])
                ->setGc($grandTourData['gc'])
                ->setCyclist($cyclist);

            $this->entityManager->persist($grandTour);
        }

        $this->entityManager->flush();
    }

    private function processCyclistClassics(Cyclist $cyclist, array $classicsData): void
    {
        foreach ($classicsData as $classicData) {
            $classic = new Classic();
            $classic
                ->setName($classicData['classic'])
                ->setSeason($classicData['season'])
                ->setResult($classicData['result'])
                ->setCyclist($cyclist);

            $this->entityManager->persist($classic);
        }

        $this->entityManager->flush();
    }

    private function processCyclistRace(User $user, Race $race, Cyclist $cyclist, int $dorsal): void
    {
        $cyclistRace = $this->cyclistRaceService->getByUserCyclistRace($user, $cyclist, $race);
        if ($cyclistRace instanceof CyclistRace) {
            $cyclistRace->setDorsal($dorsal);
            $this->cyclistRaceService->edit($cyclistRace);
            return;
        }

        $cyclistRace = new CyclistRace();
        $cyclistRace
            ->setDorsal($dorsal)
            ->setRace($race)
            ->setCyclist($cyclist)
            ->setUser($user);

        $this->logger->info(
            sprintf('Cyclist %s data processed for user %s', $cyclist->getName(), $user->getUsername())
        );

        $this->cyclistRaceService->create($cyclistRace);
    }
}
