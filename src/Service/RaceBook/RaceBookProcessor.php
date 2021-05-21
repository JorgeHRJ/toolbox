<?php

namespace App\Service\RaceBook;

use App\Entity\Asset;
use App\Entity\Classic;
use App\Entity\Cyclist;
use App\Entity\CyclistRace;
use App\Entity\GrandTour;
use App\Entity\Race;
use App\Entity\Stage;
use App\Entity\StageAsset;
use App\Entity\StageUser;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Win;
use App\Service\AssetService;
use App\Service\CyclistRaceService;
use App\Service\CyclistService;
use App\Service\RaceService;
use App\Service\StorageService;
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
    private StorageService $storageService;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private SluggerInterface $slugger;

    public function __construct(
        RaceService $raceService,
        TeamService $teamService,
        CyclistService $cyclistService,
        CyclistRaceService $cyclistRaceService,
        StorageService $storageService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        SluggerInterface $slugger
    ) {
        $this->raceService = $raceService;
        $this->teamService = $teamService;
        $this->cyclistService = $cyclistService;
        $this->cyclistRaceService = $cyclistRaceService;
        $this->storageService = $storageService;
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
        $year = $raceData['start_date']->format('Y');
        $race = $this->raceService->getByNameAndYear($raceData['name'], $year);
        if ($race instanceof Race) {
            return $race;
        }

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

    public function processStagesData(User $user, Race $race, array $stagesData): void
    {
        foreach ($stagesData as $stageData) {
            $stage = new Stage();
            $stage
                ->setRace($race)
                ->setNumber($stageData['number'])
                ->setDate($stageData['date'])
                ->setDistance($stageData['distance'])
                ->setVertical($stageData['vertical'])
                ->setDeparture($stageData['departure'])
                ->setArrival($stageData['arrival'])
                ->setType($stageData['type']);
            $this->entityManager->persist($stage);

            $stageUser = new StageUser();
            $stageUser
                ->setUser($user)
                ->setStage($stage)
                ->setComment([]);
            $this->entityManager->persist($stageUser);

            $this->entityManager->flush();

            foreach ($stageData['images'] as $imageData) {
                list($url, $title) = array_values($imageData);
                $urlParts = explode('/', $url);
                $filename = array_pop($urlParts);
                $filenameParts = explode('.', $filename);
                $extension = array_pop($filenameParts);

                $basePath = sprintf('%s/%s', StorageService::STAGES_FOLDER, $stage->getId());
                $folder = $this->storageService->getAssetsDir($basePath);

                try {
                    $this->storageService->saveFromUrl($url, $folder, $filename);
                } catch (\Exception $e) {
                    $this->logger->error(
                        sprintf(
                            'Stage (ID %d) profile image from URL %s was not saved. Error: %s',
                            $stage->getId(),
                            $url,
                            $e->getMessage()
                        )
                    );
                    continue;
                }

                $asset = new Asset();
                $asset
                    ->setFilename($filename)
                    ->setExtension($extension)
                    ->setPath($this->storageService->getAssetPath($basePath, $filename))
                    ->setType(Asset::IMAGE_TYPE)
                    ->setOrigin(Asset::STAGES_ORIGIN);

                $this->entityManager->persist($asset);

                $stageAsset = new StageAsset();
                $stageAsset
                    ->setTitle($title)
                    ->setStage($stage)
                    ->setAsset($asset);

                $this->entityManager->persist($stageAsset);
                $this->entityManager->flush();
            }
        }
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
