<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Link as CrawlerLink;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RaceService
{
    const PCS_RIDER_SEARCHER_BASE_URL = 'https://www.procyclingstats.com/rider.php';
    const PCS_RIDER_STATISTICS_BASE_URL = 'https://www.procyclingstats.com/rider/{cyclist-slug}/statistics';

    const GRAND_TOUR_STATISTICS_PATH = 'grand-tour-starts';
    const TOP_CLASSICS_STATISTICS_PATH = 'top-classic-results';

    const PCS_RIDER_SEARCHER_WINS_PARAMS = 'type={type}&id={cyclist-slug}&p=statistics&s=wins';
    const GC_WIN_TYPE = 'gc';
    const STAGES_WIN_TYPE = 'stages';
    const CLASSICS_WIN_TYPE = 'classics';
    const ITT_WIN_TYPE = 'itt';
    const WIN_TYPES = [self::GC_WIN_TYPE, self::STAGES_WIN_TYPE, self::CLASSICS_WIN_TYPE, self::ITT_WIN_TYPE];

    const RACE_INFO_MAP = [
        'Startdate' => 'start_date',
        'Enddate' => 'end_date',
        'Category' => 'category',
        'UCI Tour' => 'uci_tour'
    ];

    private LoggerInterface $logger;
    private HttpClientInterface $client;

    public function __construct(LoggerInterface $logger, HttpClientInterface $client)
    {
        $this->logger = $logger;
        $this->client = $client;
    }

    public function process(User $user, string $url): void
    {
        $urlParsed = parse_url($url);
        $baseUrl = sprintf('%s://%s', $urlParsed['scheme'], $urlParsed['host']);

        $overviewContent = $this->call($url);

        $raceData = $this->crawlRaceData($user, $url, $overviewContent);

        $startlistUrl = $this->crawlStartlistUrl($url, $baseUrl, $overviewContent);
        $startlistContent = $this->call($startlistUrl);

        $teamsData = $this->crawlTeamsData($startlistContent, $baseUrl);

        $this->processTeamsData($teamsData);
    }

    private function call(string $url): string
    {
        $response = $this->client->request('GET', $url);
        $statusCode = $response->getStatusCode();

        $this->logger->info(sprintf('Request made to %s. Response status: %d', $url, $statusCode));

        if ($statusCode !== Response::HTTP_OK) {
            throw new \Exception('No se ha podido obtener información sobre la URL indicada.');
        }

        try {
            return $response->getContent();
        } catch (HttpExceptionInterface | TransportExceptionInterface $e) {
            $this->logger->error(sprintf('Error when trying to get content from response: %s', $e->getMessage()));
            throw new \Exception('No se ha podido obtener información sobre la URL indicada.');
        }
    }

    private function crawlRaceData(User $user, string $url, string $content): array
    {
        $race = ['user' => $user, 'url' => $url];

        $crawler = new Crawler($content);
        $infoItems = $crawler->filter('ul.infolist li');
        /** @var \DOMElement $item */
        foreach ($infoItems as $item) {
            $itemsParts = explode(':', $item->textContent);
            if (count($itemsParts) < 2) {
                continue;
            }

            list($property, $data) = $itemsParts;
            if (!in_array($property, array_keys(self::RACE_INFO_MAP))) {
                continue;
            }

            $propertyKey = self::RACE_INFO_MAP[$property];
            $data = trim($data);

            $date = \DateTime::createFromFormat('Y-m-d', $data);
            $race[$propertyKey] = $date !== false ? $date : $data;
        }

        return $race;
    }

    private function crawlStartlistUrl(string $url, string $baseUrl, string $content): ?string
    {
        $crawler = new Crawler($content);
        $nodes = $crawler
            ->filter('.page-topnav li.reg > a')
            ->reduce(function (Crawler $node) {
                return strpos(strtolower($node->text()), 'startlist') !== false;
            });

        $url = null;
        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            $link = new CrawlerLink($node, $baseUrl);
            $url = $link->getUri();
            break;
        }

        return $url;
    }

    private function crawlTeamsData(string $startlistContent, string $baseUrl): array
    {
        $crawler = new Crawler($startlistContent);
        return $crawler
            ->filter('li.team')
            ->each(function (Crawler $node) use ($baseUrl) {
                $team = $node->filter('b a')->text();
                $cyclistsData = $node
                    ->filter('ul li')
                    ->each(function (Crawler $node) use ($baseUrl) {
                        $linkNodes = $node->filter('a');
                        $cyclistUrl = null;
                        /** @var \DOMElement $linkNode */
                        foreach ($linkNodes as $linkNode) {
                            $link = new CrawlerLink($linkNode, $baseUrl);
                            $cyclistUrl = $link->getUri();
                            break;
                        }

                        if ($cyclistUrl !== null) {
                            $match = null;
                            preg_match_all('/^(\d+)([^\d]+)/', $node->text(), $match);
                            if (isset($match[1]) && isset($match[2])) {
                                $dorsal = $match[1][0];
                                $name = $match[2][0];
                                $cyclistData = ['name' => $name, 'dorsal' => $dorsal];

                                $cyclistContent = $this->call($cyclistUrl);
                                $cyclistData = array_merge($cyclistData, $this->crawlCyclistData($cyclistContent));

                                $cyclistUrlParts = explode('/', $cyclistUrl);
                                $cyclistSlug = array_pop($cyclistUrlParts);

                                $cyclistData['wins'] = $this->crawlCyclistWinsData($cyclistSlug);
                                $cyclistData['grand_tours'] = $this->crawlCyclistGrandToursData($cyclistSlug);
                                $cyclistData['classics'] = $this->crawlCyclistClassicsData($cyclistSlug);

                                return $cyclistData;
                            }
                        }

                        return [];
                    });

                return ['team' => $team, 'cyclists' => $cyclistsData];
            });
    }

    private function crawlCyclistData(string $cyclistContent): array
    {
        $crawler = new Crawler($cyclistContent);
        $cyclistInfo = $crawler
            ->filter('div .rdr-info-cont')
            ->first();

        $info = $cyclistInfo->text();

        $birthdate = strpos($info, 'Nationality') !== false
            ? substr($info, 0, strpos($info, 'Nationality'))
            : null;
        if ($birthdate !== null) {
            $info = str_replace($birthdate, '', $info);
            $birthdate = trim(str_replace('Date of birth:', '', $birthdate));
            $birthdate = trim(preg_replace('(\(\d+\))', '', $birthdate));
            $birthdate = \DateTime::createFromFormat('dS F Y', $birthdate);
        }

        $nationality = strpos($info, 'Weight') !== false
            ? substr($info, 0, strpos($info, 'Weight'))
            : null;
        if ($nationality !== null) {
            $info = str_replace($nationality, '', $info);
            $nationality = str_replace('Nationality:', '', $nationality);
        }

        $weight = strpos($info, 'Height') !== false
            ? substr($info, 0, strpos($info, 'Height'))
            : null;
        if ($weight !== null) {
            $info = str_replace($weight, '', $info);
            $weight = str_replace('Weight:', '', $weight);
        }

        $height = strpos($info, 'Place of birth') !== false
            ? substr($info, 0, strpos($info, 'Place of birth'))
            : null;
        if ($height !== null) {
            $info = str_replace($height, '', $info);
            $height = str_replace('Height:', '', $height);
        }

        $location = strpos($info, 'Points per specialty') !== false
            ? substr($info, 0, strpos($info, 'Points per specialty'))
            : null;
        if ($location !== null) {
            $info = str_replace($location, '', $info);
            $location = str_replace('Place of birth:', '', $location);
        }

        $data = [
            'birthdate' => $birthdate,
            'nationality' => $nationality,
            'weight' => $weight,
            'height' => $height,
            'location' => $location
        ];

        array_walk($data, function (&$value) {
            if (is_string($value)) {
                $value = preg_replace('/\xc2\xa0/', ' ', $value);
                $value = trim($value);
            }
        });

        return $data;
    }

    private function crawlCyclistWinsData(string $cyclistSlug): array
    {
        $winsData = [];

        $raceTableIndex = null;
        $classTableIndex = null;
        $dateTableIndex = null;

        foreach (self::WIN_TYPES as $type) {
            $url = sprintf(
                '%s?%s',
                self::PCS_RIDER_SEARCHER_BASE_URL,
                str_replace(['{type}', '{cyclist-slug}'], [$type, $cyclistSlug], self::PCS_RIDER_SEARCHER_WINS_PARAMS)
            );
            $winContent = $this->call($url);

            $crawler = new Crawler($winContent);

            if ($raceTableIndex === null || $classTableIndex === null || $dateTableIndex === null) {
                $headValues = $crawler
                    ->filter('table.basic thead tr')
                    ->first()
                    ->children()
                    ->each(function (Crawler $node) {
                        return $node->text();
                    });
                if (empty($headValues)) {
                    continue;
                }

                $raceTableIndex = (int) array_search('Race', $headValues);
                $classTableIndex = (int) array_search('Class', $headValues);
                $dateTableIndex = (int) array_search('Date', $headValues);
            }

            $typeWins = $crawler
                ->filter('table.basic tbody tr')
                ->each(function (Crawler $node) {
                    return $node->children('td')->each(function (Crawler $subNode) {
                        return $subNode->text();
                    });
                });

            if (empty($typeWins)) {
                continue;
            }

            foreach ($typeWins as $typeWin) {
                $winsData[] = [
                    'type' => $type,
                    'race' => $typeWin[$raceTableIndex],
                    'class' => $typeWin[$classTableIndex],
                    'date' => new \DateTime($typeWin[$dateTableIndex])
                ];
            }

        }

        return $winsData;
    }

    private function crawlCyclistGrandToursData(string $cyclistSlug): array
    {
        $data = [];

        // get page content
        $url = sprintf(
            '%s/%s',
            str_replace('{cyclist-slug}', $cyclistSlug, self::PCS_RIDER_STATISTICS_BASE_URL),
            self::GRAND_TOUR_STATISTICS_PATH
        );
        $content = $this->call($url);
        $crawler = new Crawler($content);

        // get table indexes
        $headValues = $crawler
            ->filter('table.basic thead tr')
            ->first()
            ->children()
            ->each(function (Crawler $node) {
                return $node->text();
            });

        $seasonTableIndex = (int) array_search('Season', $headValues);
        $grandTourTableIndex = (int) array_search('Grand tour', $headValues);
        $gcTableIndex = (int) array_search('GC', $headValues);

        $typeWins = $crawler
            ->filter('table.basic tbody tr')
            ->each(function (Crawler $node) {
                return $node->children('td')->each(function (Crawler $subNode) {
                    return $subNode->text();
                });
            });

        if (empty($typeWins)) {
            return [];
        }

        foreach ($typeWins as $typeWin) {
            $data[] = [
                'season' => $typeWin[$seasonTableIndex],
                'grand_tour' => $typeWin[$grandTourTableIndex],
                'gc' => $typeWin[$gcTableIndex]
            ];
        }

        return $data;
    }

    private function crawlCyclistClassicsData(string $cyclistSlug): array
    {
        $data = [];

        // get page content
        $url = sprintf(
            '%s/%s',
            str_replace('{cyclist-slug}', $cyclistSlug, self::PCS_RIDER_STATISTICS_BASE_URL),
            self::TOP_CLASSICS_STATISTICS_PATH
        );
        $content = $this->call($url);
        $crawler = new Crawler($content);

        // get table indexes
        $headValues = $crawler
            ->filter('table.basic thead tr')
            ->first()
            ->children()
            ->each(function (Crawler $node) {
                return $node->text();
            });

        $seasonTableIndex = (int) array_search('Season', $headValues);
        $classicTableIndex = (int) array_search('Classic', $headValues);
        $resultTableIndex = (int) array_search('Result', $headValues);

        $typeWins = $crawler
            ->filter('table.basic tbody')
            ->first()
            ->children('tr')
            ->each(function (Crawler $node) {
                return $node->children('td')->each(function (Crawler $subNode) {
                    return $subNode->text();
                });
            });

        if (empty($typeWins)) {
            return [];
        }

        foreach ($typeWins as $typeWin) {
            $data[] = [
                'season' => $typeWin[$seasonTableIndex],
                'classic' => $typeWin[$classicTableIndex],
                'result' => $typeWin[$resultTableIndex]
            ];
        }

        return $data;
    }
}
