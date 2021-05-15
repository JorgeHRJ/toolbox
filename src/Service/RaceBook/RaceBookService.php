<?php

namespace App\Service\RaceBook;

use App\Entity\User;

class RaceBookService
{
    private RaceBookClient $client;
    private RaceBookCrawler $crawler;
    private RaceBookProcessor $processor;

    public function __construct(RaceBookClient $client, RaceBookCrawler $crawler, RaceBookProcessor $processor)
    {
        $this->client = $client;
        $this->crawler = $crawler;
        $this->processor = $processor;
    }

    public function process(User $user, string $url): void
    {
        $urlParsed = parse_url($url);
        $baseUrl = sprintf('%s://%s', $urlParsed['scheme'], $urlParsed['host']);

        $overviewContent = $this->client->call($url);

        $raceData = $this->crawler->crawlRaceData($url, $overviewContent);
        $race = $this->processor->processRaceData($raceData);
        unset($raceData);

        $startlistUrl = $this->crawler->crawlStartlistUrl($baseUrl, $overviewContent);
        $startlistContent = $this->client->call($startlistUrl);

        $teamsData = $this->crawler->crawlTeamsData($startlistContent, $baseUrl);

        $this->processor->processTeamsData($user, $race, $teamsData);
    }
}
