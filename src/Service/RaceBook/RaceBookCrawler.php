<?php

namespace App\Service\RaceBook;

use Symfony\Component\DomCrawler\Link as CrawlerLink;
use Symfony\Component\DomCrawler\Crawler;

class RaceBookCrawler
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

    private RaceBookClient $client;

    public function __construct(RaceBookClient $client)
    {
        $this->client = $client;
    }

    public function crawlRaceData(string $url, string $content): array
    {
        $race = ['url' => $url];

        $crawler = new Crawler($content);

        $nameLabel = $crawler
            ->filter('.page-content .text-regular')
            ->first()
            ->text();
        $nameLabel = trim(str_replace('Preview statistics for', '', $nameLabel));
        $name = preg_replace('/(\d{4})\./', '', $nameLabel);
        $race['name'] = trim($name);

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

    public function crawlNavUrl(string $baseUrl, string $content, string $type): ?string
    {
        $crawler = new Crawler($content);
        $nodes = $crawler
            ->filter('.page-topnav li.reg > a')
            ->reduce(function (Crawler $node) use ($type) {
                return strpos(strtolower($node->text()), $type) !== false;
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

    public function crawlStagesData(string $stagesContent, string $baseUrl): array
    {
        $crawler = new Crawler($stagesContent);
        $tableHeaders = $crawler
            ->filter('table.basic thead tr')
            ->first()
            ->children()
            ->each(function (Crawler $node) {
                return $node->text();
            });

        $raceTableIndex = (int) array_search('Race', $tableHeaders);

        $stagesNodes = $crawler
            ->filter('table.basic tbody')
            ->first()
            ->children('tr')
            ->each(function (Crawler $node) {
                return $node->children('td')->each(function (Crawler $subNode) {
                    return $subNode;
                });
            });

        $stagesLinks = [];
        foreach ($stagesNodes as $stagesNode) {
            /** @var Crawler $cell */
            $cell = $stagesNode[$raceTableIndex];
            $link = $cell->filter('a')->first()->attr('href');
            $stagesLinks[] = sprintf('%s/%s', $baseUrl, $link);
        }

        $stagesData = [];
        foreach ($stagesLinks as $stageNumber => $stageLink) {
            $stageContent = $this->client->call($stageLink);
            $crawler = new Crawler($stageContent);
            $infoText = $crawler
                ->filter('ul.infolist')
                ->text();

            $keys = [
                'Avg. speed winner', 'Race category', 'Distance', 'Points scale', 'Parcours type', 'ProfileScore',
                'Vert. meters', 'Departure', 'Arrival', 'Race ranking', 'Won how'
            ];
            $info = [];
            foreach ($keys as $key) {
                $parts = explode($key, $infoText);
                if (count($parts) > 1) {
                    $item = $parts[0];
                    $infoText = str_replace($item, '', $infoText);
                    list($field, $value) = explode(':', $item);
                    $info[$field] = trim($value);
                }
            }

            $date = $info['Date'] ?? null;
            if ($date !== null) {
                list($date) = explode(',', $date);
                $date = \DateTime::createFromFormat('d F Y', $date);
            }

            $distance = $info['Distance'] ?? null;
            $vertical = $info['Vert. meters'] ?? null;
            $departure = $info['Departure'] ?? null;
            $arrival = $info['Arrival'] ?? null;

            $parcourClass = $crawler
                ->filter('ul.infolist span.icon.profile')
                ->attr('class');
            $parcourClass = trim(str_replace('icon profile', '', $parcourClass));
            $parcour = null;
            switch ($parcourClass) {
                case 'p1':
                    $parcour = 'Llano';
                    break;
                case 'p2':
                    $parcour = 'Cotas, final en llano';
                    break;
                case 'p3':
                    $parcour = 'Cotas, final en alto';
                    break;
                case 'p4':
                    $parcour = 'Montañosa, final en llano';
                    break;
                case 'p5':
                    $parcour = 'Montañosa, final en alto';
                    break;
            }

            $imagesData = $this->crawlStagesProfilesImages($crawler, $baseUrl);
            $stagesData[] = [
                'number' => ((int) $stageNumber) + 1,
                'date' => $date,
                'distance' => $distance,
                'vertical' => sprintf('%s m', $vertical),
                'departure' => $departure,
                'arrival' => $arrival,
                'type' => $parcour,
                'images' => $imagesData
            ];
        }

        return $stagesData;
    }

    public function crawlTeamsData(string $startlistContent, string $baseUrl): array
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

                                $cyclistData = ['dorsal' => $dorsal];

                                $cyclistContent = $this->client->call($cyclistUrl);
                                $cyclistData['name'] = $this->getCyclistName($cyclistContent);

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

    public function crawlCyclistData(string $cyclistContent): array
    {
        $crawler = new Crawler($cyclistContent);
        $cyclistInfo = $crawler
            ->filter('div .rdr-info-cont')
            ->first();

        $cyclistInfo = $cyclistInfo->text();

        $keys = ['Nationality', 'Weight', 'Height', 'Place of birth', 'LIVE', 'Points per speciality'];
        $info = [];
        foreach ($keys as $key) {
            $parts = explode($key, $cyclistInfo);
            if (count($parts) > 1) {
                $item = $parts[0];
                $cyclistInfo = str_replace($item, '', $cyclistInfo);
                list($field, $value) = explode(':', $item);
                $info[$field] = trim($value);
            }
        }

        $birthdate = $info['Date of birth'] ?? null;
        if ($birthdate !== null) {
            $birthdate = trim(preg_replace('(\(\d+\))', '', $birthdate));
            $birthdate = \DateTime::createFromFormat('dS F Y', $birthdate);
        }

        $nationality = $info['Nationality'] ?? null;
        $weight = $info['Weight'] ?? null;
        $height = $info['Height'] ?? null;
        $location = $info['Place of birth'] ?? null;

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

    public function crawlCyclistWinsData(string $cyclistSlug): array
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
            $winContent = $this->client->call($url);

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

    public function crawlCyclistGrandToursData(string $cyclistSlug): array
    {
        $data = [];

        // get page content
        $url = sprintf(
            '%s/%s',
            str_replace('{cyclist-slug}', $cyclistSlug, self::PCS_RIDER_STATISTICS_BASE_URL),
            self::GRAND_TOUR_STATISTICS_PATH
        );
        $content = $this->client->call($url);
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

    public function crawlCyclistClassicsData(string $cyclistSlug): array
    {
        $data = [];

        // get page content
        $url = sprintf(
            '%s/%s',
            str_replace('{cyclist-slug}', $cyclistSlug, self::PCS_RIDER_STATISTICS_BASE_URL),
            self::TOP_CLASSICS_STATISTICS_PATH
        );
        $content = $this->client->call($url);
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

    private function crawlStagesProfilesImages(Crawler $stageCrawler, string $baseUrl): array
    {
        $profilesRelativeLink = $stageCrawler
            ->filter('ul.list a img')
            ->first()
            ->closest('a')
            ->attr('href');
        $profilesUrl = sprintf('%s/%s', $baseUrl, $profilesRelativeLink);

        $profilesContent = $this->client->call($profilesUrl);
        $profilesCrawler = new Crawler($profilesContent);
        return $profilesCrawler
            ->filter('.page-content.page-object.default img')
            ->each(function (Crawler $node) use ($baseUrl) {
                return [
                    'url' => sprintf('%s/%s', $baseUrl, $node->attr('src')),
                    'title' => $node->closest('li')->text()
                ];
            });
    }

    private function getCyclistName(string $cyclistContent): string
    {
        $crawler = new Crawler($cyclistContent);
        $cyclistNameNode = $crawler
            ->filter('.page-title h1')
            ->first();

        $cyclistName = $cyclistNameNode->text();
        return preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $cyclistName);
    }
}
