<?php

namespace App\Service;

use App\Entity\Reservoir;
use App\Entity\ReservoirData;
use App\Entity\ReservoirMunicipality;
use App\Entity\ReservoirProcess;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\ReservoirDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Smalot\PdfParser\Parser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Link as CrawlerLink;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReservoirDataService extends BaseService
{
    const LA_PALMA_AGUAS_BASE_URL = 'https://lapalmaaguas.com/category/balsas/';
    const LA_PALMA_AGUAS_PAGINATION = 'page/%d/';

    private $processService;
    private $municipalityService;
    private $reservoirService;
    private $storageService;
    private $client;
    private $pdfParser;

    /** @var ReservoirDataRepository */
    private $repository;

    public function __construct(
        ReservoirProcessService $processService,
        ReservoirMunicipalityService $municipalityService,
        ReservoirService $reservoirService,
        StorageService $storageService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        HttpClientInterface $client
    ) {
        parent::__construct($entityManager, $logger);
        $this->processService = $processService;
        $this->municipalityService = $municipalityService;
        $this->reservoirService = $reservoirService;
        $this->storageService = $storageService;
        $this->client = $client;
        $this->pdfParser = new Parser();
        $this->repository = $entityManager->getRepository(ReservoirData::class);
    }

    public function getData(): array
    {
        return $this->repository->getData();
    }

    public function getSortFields(): array
    {
        return [];
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }

    /**
     * @param Reservoir $reservoir
     * @param ReservoirProcess $process
     * @param int $fillness
     * @return ReservoirData
     * @throws \Exception
     */
    public function new(Reservoir $reservoir, ReservoirProcess $process, int $fillness): ReservoirData
    {
        $data = new ReservoirData();

        $data->setFillness($fillness);
        $data->setProcess($process);
        $data->setReservoir($reservoir);

        return $this->create($data);
    }

    /**
     * @throws \Throwable
     */
    public function processData(): void
    {
        $processedDates = $this->processService->getProcessedDates();

        $articlesLinks = $this->getArticlesLinks($processedDates, self::LA_PALMA_AGUAS_BASE_URL);

        $processes = $this->getProcessesFromArticles($articlesLinks);

        $this->handle($processes);
    }

    /**
     * @param ReservoirProcess[]|array $processes
     */
    private function handle(array $processes): void
    {
        $municipalities = $this->municipalityService->getNames();

        foreach ($processes as $process) {
            $parsedItems = $this->parse($process, $municipalities);
            foreach ($parsedItems as $item) {
                $municipality = $this->municipalityService->getByName($item['municipality_name']);
                if (!$municipality instanceof ReservoirMunicipality) {
                    continue;
                }

                $reservoir = $this->getOrCreateReservoir($municipality, $item['reservoir_name'], $item['capacity']);

                $this->new($reservoir, $process, $item['fillness']);
            }
        }
    }

    /**
     * @param ReservoirMunicipality $municipality
     * @param string $reservoirName
     * @param int $capacity
     * @return Reservoir
     * @throws \Exception
     */
    private function getOrCreateReservoir(
        ReservoirMunicipality $municipality,
        string $reservoirName,
        int $capacity
    ): Reservoir {
        $reservoir = $this->reservoirService->getByName($reservoirName);
        if ($reservoir instanceof Reservoir) {
            return $reservoir;
        }

        return $this->reservoirService->new($municipality, $reservoirName, $capacity);
    }

    /**
     * @param ReservoirProcess $process
     * @param array $municipalities
     * @return array
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function parse(ReservoirProcess $process, array $municipalities): array
    {
        $path = sprintf(
            '%s/%s/%s',
            $this->storageService->getStorageFolder(),
            StorageService::RESERVOIR_FOLDER,
            $process->getFilename()
        );
        $document = $this->pdfParser->parseFile($path);
        $page = $document->getPages()[0];
        $text = $page->getText();

        $rows = array_map('trim', preg_split('/\R/', $text));
        $rows = array_reverse($rows);

        $reservoirRows = [];
        $aloneNumber = null;
        foreach ($rows as $row) {
            if (strlen($row) == 2 && is_numeric($row)) {
                $aloneNumber = $row;
                continue;
            }

            foreach ($municipalities as $municipality) {
                if (strpos($row, $municipality) !== false) {
                    if ($aloneNumber !== null) {
                        $row = sprintf('%s %s', $row, $aloneNumber);
                        $aloneNumber = null;
                    }
                    $reservoirRows[] = $row;
                }
            }
        }

        $result = [];
        foreach ($reservoirRows as $row) {
            $row = preg_replace('/(\v|\s)+/', ' ', $row);
            $aux = array_reverse(explode(' ', $row));

            $fillness = $aux[0];
            $capacity = (int) str_replace('.', '', $aux[1]);
            unset($aux[0]);
            unset($aux[1]);

            $aux = implode(' ', array_reverse($aux));

            $municipalityName = null;
            $reservoirName = null;
            foreach ($municipalities as $municipality) {
                if (strpos($aux, $municipality) !== false) {
                    $municipalityName = $municipality;

                    $reservoirName = trim(str_replace($municipality, '', $aux));
                    $reservoirName = preg_replace('/[^a-zA-Z]+/', ' ', $reservoirName);
                    $reservoirName = trim($reservoirName);
                }
            }

            if ($municipalityName !== null && $reservoirName !== null) {
                $result[] = [
                    'reservoir_name' => $reservoirName,
                    'municipality_name' => $municipalityName,
                    'capacity' => $capacity,
                    'fillness' => $fillness
                ];
            }
        }

        return $result;
    }

    private function getProcessesFromArticles(array $links): array
    {
        $processes = [];
        $folder = $this->getReservoirFolder();

        foreach ($links as $data) {
            $pdfLink = $this->getPdfLinkFromArticle($data['link']);
            if ($pdfLink === null) {
                continue;
            }

            $date = $data['date'];
            $filename = sprintf('reservoirs_%s.pdf', str_replace('/', '', $date));
            try {
                $this->storageService->saveRemote($pdfLink, $folder, $filename);
            } catch (\Exception $e) {
                continue;
            }

            $processes[] = $this->processService->new($filename, $date);
        }

        return $processes;
    }

    /**
     * @param array $dates
     * @param string $baseUrl
     * @param int|null $page
     * @return array
     * @throws \Throwable
     */
    private function getArticlesLinks(array $dates, string $baseUrl, int $page = null): array
    {
        $url = $page !== null
            ? sprintf('%s%s', $baseUrl, sprintf(self::LA_PALMA_AGUAS_PAGINATION, $page))
            : $baseUrl;

        $response = $this->client->request('GET', $url);
        $statusCode = $response->getStatusCode();

        $this->logger->info(sprintf('Request made to %s. Response status: %d', $url, $statusCode));

        if ($statusCode !== Response::HTTP_OK) {
            return [];
        }

        $links = $this->crawlForLinks($dates, $baseUrl, $response->getContent());

        $page = $page !== null ? $page + 1 : 2;
        $links = array_merge($links, $this->getArticlesLinks($dates, $baseUrl, $page));

        return $links;
    }

    /**
     * @param array $dates
     * @param string $baseUrl
     * @param string $content
     * @return array
     */
    private function crawlForLinks(array $dates, string $baseUrl, string $content): array
    {
        $result = [];

        $crawler = new Crawler($content);
        $nodes = $crawler
            ->filter('.entry-title a')
            ->reduce(function (Crawler $node) use ($dates) {
                return !$this->hasDate($dates, $node->text());
            });

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            $link = new CrawlerLink($node, $baseUrl);
            $date = $this->extractDate($link->getNode()->textContent);
            if ($date === null) {
                continue;
            }

            $result[] = ['link' => $link->getUri(), 'date' => $date];
        }

        return $result;
    }

    /**
     * @param array $dates
     * @param string $text
     * @return bool
     */
    private function hasDate(array $dates, string $text): bool
    {
        foreach ($dates as $date) {
            if (strpos($date, $text) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $text
     * @return string|null
     */
    private function extractDate(string $text): ?string
    {
        $matches = null;
        preg_match_all('/\d{2}\/\d{2}\/\d{4}/', $text, $matches);

        if (!isset($matches[0][0])) {
            return null;
        }

        return $matches[0][0];
    }

    /**
     * @param string $url
     * @return string|null
     * @throws \Throwable
     */
    private function getPdfLinkFromArticle(string $url): ?string
    {
        $response = $this->client->request('GET', $url);
        $content = $response->getContent();

        $crawler = new Crawler($content);
        $nodes = $crawler->filter('.entry-summary a');

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            $element = new CrawlerLink($node);
            return $element->getUri();
        }

        return null;
    }

    private function getReservoirFolder(): string
    {
        return sprintf('%s/%s', $this->storageService->getStorageFolder(), StorageService::RESERVOIR_FOLDER);
    }
}
