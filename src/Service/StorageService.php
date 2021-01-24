<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class StorageService
{
    const RESERVOIR_FOLDER = 'reservoir';

    private $storageFolder;
    private $logger;

    public function __construct(string $storageFolder, LoggerInterface $logger)
    {
        $this->storageFolder = $storageFolder;
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getStorageFolder(): string
    {
        return $this->storageFolder;
    }

    /**
     * Save a file in the filesystem from a remote url
     *
     * @param string $url
     * @param string $folder
     * @param string $filename
     * @throws \Exception
     */
    public function saveRemote(string $url, string $folder, string $filename): void
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0764, true);
        }

        try {
            file_put_contents(sprintf('%s/%s', $folder, $filename), file_get_contents($url));
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error when saving file "%s". Error: %s', $filename, $e->getMessage()));

            throw $e;
        }
    }
}
