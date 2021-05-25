<?php

namespace App\Service\Irrigation;

use App\Entity\IrrigationData;
use App\Entity\IrrigationProcess;
use App\Entity\IrrigationStat;
use App\Entity\IrrigationZone;
use App\Library\Crawler\BaseCrawlerClient;
use App\Service\IrrigationDataService;
use App\Service\IrrigationProcessService;
use App\Service\IrrigationZoneService;
use App\Service\StorageService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Smalot\PdfParser\Parser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IrrigationService extends BaseCrawlerClient
{
    const LA_PALMA_AGUAS_BASE_URL = 'https://lapalmaaguas.com/category/actualidad/';
    const LA_PALMA_AGUAS_PAGINATION = 'page/%d/';

    private IrrigationDataService $dataService;
    private IrrigationZoneService $zoneService;
    private IrrigationProcessService $processService;
    private StorageService $storageService;
    private EntityManagerInterface $entityManager;
    private Parser $pdfParser;
    private array $errors = [];

    public function __construct(
        IrrigationDataService $dataService,
        IrrigationZoneService $zoneService,
        IrrigationProcessService $processService,
        StorageService $storageService,
        EntityManagerInterface $entityManager,
        HttpClientInterface $client,
        LoggerInterface $logger
    ) {
        parent::__construct($client, $logger);
        $this->dataService = $dataService;
        $this->zoneService = $zoneService;
        $this->processService = $processService;
        $this->storageService = $storageService;
        $this->entityManager = $entityManager;
        $this->pdfParser = new Parser();
    }

    public function process(): void
    {
        $startDates = $this->dataService->getStartDates();

        $linksData = $this->getArticlesLinks($startDates, self::LA_PALMA_AGUAS_BASE_URL);
        $processes = $this->processLinks($linksData);
        $parsedData = $this->parseProcesses($processes);

        $process = $this->processService->create();
        $this->processParsedData($process, $parsedData);

        $this->processService->update($process, $this->errors);
        // TODO refactor after finished funcionality
    }

    private function getArticlesLinks(array $dates, string $baseUrl, int $page = null): array
    {
        $url = $page !== null
            ? sprintf('%s%s', $baseUrl, sprintf(self::LA_PALMA_AGUAS_PAGINATION, $page))
            : $baseUrl;

        try {
            $articleContent = $this->call($url);
        } catch (\Exception $e) {
            return [];
        }

        $links = $this->crawlLinks($dates, $baseUrl, $articleContent);

        $page = $page !== null ? $page + 1 : 2;
        return array_merge($links, $this->getArticlesLinks($dates, $baseUrl, $page));
    }

    private function crawlLinks(array $processedDates, string $baseUrl, string $articleContent): array
    {
        $crawler = new Crawler($articleContent);
        $linksData = $crawler
            ->filter('.entry-title a')
            ->each(function (Crawler $node) use ($processedDates, $baseUrl) {
                $extracted = $this->extractDates($node->text());
                if ($extracted !== null && !$this->isDateProcessed($processedDates, $extracted['start_date'])) {
                    $link = strpos($node->attr('href'), 'http') !== false
                        ? $node->attr('href')
                        : sprintf('%s%s', $baseUrl, $node->attr('href'));
                    return ['dates' => $extracted, 'link' => $link];
                }

                return [];
            });

        return array_filter($linksData, function ($linkData) {
            return !empty($linkData);
        });
    }

    private function processLinks(array $linksData): array
    {
        $processes = [];
        $folder = $this->getIrrigationFolder();

        foreach ($linksData as $data) {
            $articleContent = $this->call($data['link']);
            $pdfLink = $this->crawlPdfLink($articleContent);
            if ($pdfLink === null) {
                $this->errors[] = sprintf('PDF link not found in %s', $data['link']);
                continue;
            }

            /** @var \DateTime $startDate */
            $startDate = $data['dates']['start_date'];
            /** @var \DateTime $endDate */
            $endDate = $data['dates']['end_date'];
            $filename = sprintf(
                'irrigations_recommendation_%s_%s.pdf',
                $startDate->format('dmY'),
                $endDate->format('dmY')
            );

            try {
                $this->storageService->saveRemote($pdfLink, $folder, $filename);
            } catch (\Exception $e) {
                $this->errors[] = sprintf('PDF from %s was not downloaded. Error: %s', $pdfLink, $e->getMessage());
                continue;
            }

            $processes[] = array_merge($data, ['filename' => $filename]);
        }

        return $processes;
    }

    private function crawlPdfLink(string $articleContent): ?string
    {
        $crawler = new Crawler($articleContent);
        $pdfNode = $crawler
            ->filter('.entry-summary a[href*="pdf"]');
        if ($pdfNode->count() === 0) {
            return null;
        }
        $pdfNode = $pdfNode->first();

        return strpos($pdfNode->attr('href'), '.pdf') !== false
            ? $pdfNode->attr('href')
            : null;
    }

    private function parseProcesses(array $processes): array
    {
        $parsed = [];

        foreach ($processes as $process) {
            try {
                $parsed[] = array_merge($process, ['data' => $this->parse($process)]);
            } catch (\Exception $e) {
                $this->errors[] = sprintf('Error parsing file gotten from URL %s', $process['link']);
            }
        }

        return $parsed;
    }

    private function processParsedData(IrrigationProcess $process, array $parsedData): void
    {
        foreach ($parsedData as $item) {
            foreach ($item['data'] as $zoneName => $stats) {
                $zone = $this->zoneService->getByName($zoneName);
                if (!$zone instanceof IrrigationZone) {
                    $zone = new IrrigationZone();
                    $zone->setName($zoneName);

                    $this->entityManager->persist($zone);
                    $this->entityManager->flush();
                }

                $data = new IrrigationData();
                $data
                    ->setFilename($item['filename'])
                    ->setUrl($item['link'])
                    ->setStartDate($item['dates']['start_date'])
                    ->setEndDate($item['dates']['end_date'])
                    ->setZone($zone)
                    ->setProcess($process);

                $this->entityManager->persist($data);

                foreach ($stats as $type => $statData) {
                    foreach (['outdoors', 'indoors'] as $context) {
                        $value = trim(str_replace(',', '.', $statData[$context]));
                        if ($value === '' || !is_numeric($value)) {
                            $value = null;
                            $this->errors[] = sprintf(
                                'It seems there is a wrong parsed value in file gotten from URL %s. Value: %s',
                                $item['link'],
                                $statData[$context]
                            );
                        }

                        $stat = new IrrigationStat();
                        $stat
                            ->setType($type)
                            ->setContext($context)
                            ->setValue($value)
                            ->setData($data);

                        $this->entityManager->persist($stat);
                    }
                }
                $this->entityManager->flush();
            }
        }

        $this->entityManager->flush();
    }

    private function parse(array $process): array
    {
        $path = sprintf(
            '%s/%s/%s',
            $this->storageService->getStorageFolder(),
            StorageService::IRRIGATION_FOLDER,
            $process['filename']
        );
        $document = $this->pdfParser->parseFile($path);
        $page = $document->getPages()[0];
        $text = $page->getText();

        $rows = array_map('trim', preg_split('/\R/', $text));

        $data = [];
        $zoneTemp = null;
        $statTemp = null;
        foreach ($rows as $row) {
            $row = preg_replace('/(\v|\s)+/', ' ', $row);

            $matches = null;
            preg_match_all('/ZONA (\d+). (.*)/', $row, $matches);
            if (isset($matches[0][0])) {
                $zoneTemp = trim(str_replace('Aire Libre Invernadero', '', $matches[2][0]));
                continue;
            }

            $matches = null;
            preg_match_all(
                '/(LITROS POR PLANTA\/SEMANAL|LITROS POR PLANTA\/DÍA|PIPAS POR CELEMIN\/SEMANAL) (.*)/',
                $row,
                $matches
            );
            if (isset($matches[0][0])) {
                $stat = $this->getStatKey($matches[1][0]);
                $values = $matches[2][0];

                $valueParts = explode(' ', $values);
                $outdoors = $valueParts[0] ?? null;
                $indoors = $valueParts[1] ?? null;

                if ($zoneTemp !== null) {
                    $data[$zoneTemp][$stat] = [
                        'indoors' => $indoors,
                        'outdoors' => $outdoors
                    ];
                }
            }

            $matches = null;
            preg_match_all(
                '/^(LITROS POR PLANTA\/SEMANAL|LITROS POR PLANTA\/DÍA|PIPAS POR CELEMIN\/SEMANAL)$/',
                $row,
                $matches
            );
            if (isset($matches[0][0])) {
                $statTemp = $this->getStatKey($matches[1][0]);
                continue;
            }

            $matches = null;
            preg_match_all(
                '/^([+-]?\d+(?:\,\d+)?) ?([+-]?\d+(?:\,\d+)?)?$/',
                $row,
                $matches
            );
            if (isset($matches[0][0])) {
                if ($zoneTemp !== null && $statTemp !== null) {
                    $outdoors = $matches[1][0] ?? null;
                    $indoors = $matches[2][0] ?? null;

                    $data[$zoneTemp][$statTemp] = [
                        'indoors' => $indoors,
                        'outdoors' => $outdoors
                    ];
                }
            }

        }

        return $data;
    }

    private function extractDates(string $text): ?array
    {
        $matches = null;
        preg_match_all(
            '/Recomendación de riego en platanera del (\d+) de (\w+) al (\d+) de (\w+) de (\d+)/',
            $text,
            $matches
        );

        if (isset($matches[0][0])) {
            $startDay = $matches[1][0];
            $startMonth = $this->getMonthNumber($matches[2][0]);
            $endDay = $matches[3][0];
            $endMonth = $this->getMonthNumber($matches[4][0]);
            $year = $matches[5][0];

            $startDate = new \DateTime(sprintf('%s/%s/%s', $year, $startMonth, $startDay));
            $endDate = new \DateTime(sprintf('%s/%s/%s', $year, $endMonth, $endDay));
            if ($startDate > $endDate) {
                $startDate->modify('-1 year');
            }

            return ['start_date' => $startDate, 'end_date' => $endDate];
        }

        $matches = null;
        preg_match_all(
            '/Recomendación de riego en platanera del (\d+) al (\d+) de (\w+) de (\d+)/',
            $text,
            $matches
        );

        if (isset($matches[0][0])) {
            $startDay = $matches[1][0];
            $endDay = $matches[2][0];
            $month = $this->getMonthNumber($matches[3][0]);
            $year = $matches[4][0];

            $startDate = new \DateTime(sprintf('%s/%s/%s', $year, $month, $startDay));
            $endDate = new \DateTime(sprintf('%s/%s/%s', $year, $month, $endDay));

            return ['start_date' => $startDate, 'end_date' => $endDate];
        }

        return null;
    }

    private function isDateProcessed(array $processedDates, \DateTime $startDate): bool
    {
        foreach ($processedDates as $processedDate) {
            if ($startDate->format('Ymd') === $processedDate->format('Ymd')) {
                return true;
            }
        }

        return false;
    }

    private function getMonthNumber(string $month): string
    {
        $months = [
            'enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04', 'mayo' => '05',
            'junio' => '06', 'julio' => '07', 'agosto' => '08', 'septiembre' => '09', 'octubre' => '10',
            'noviembre' => '11', 'diciembre' => '12'
        ];

        return $months[$month];
    }

    private function getStatKey(string $stat): string
    {
        $keys = [
            'LITROS POR PLANTA/SEMANAL' => 'liters_per_week',
            'LITROS POR PLANTA/DÍA' => 'liters_per_day',
            'PIPAS POR CELEMIN/SEMANAL' => 'pipes_per_week'
        ];

        return $keys[$stat];
    }

    private function getIrrigationFolder(): string
    {
        return sprintf('%s/%s', $this->storageService->getStorageFolder(), StorageService::IRRIGATION_FOLDER);
    }
}
