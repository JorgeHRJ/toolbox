<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class StorageService
{
    const RESERVOIR_FOLDER = 'reservoir';
    const IRRIGATION_FOLDER = 'irrigation';
    const STAGES_FOLDER = 'stages';

    private string $storageFolder;
    private string $publicFolder;
    private string $assetsFolder;
    private LoggerInterface $logger;

    public function __construct(
        string $storageFolder,
        string $publicFolder,
        string $assetsFolder,
        LoggerInterface $logger
    ) {
        $this->storageFolder = $storageFolder;
        $this->publicFolder = $publicFolder;
        $this->assetsFolder = $assetsFolder;
        $this->logger = $logger;
    }

    public function getStorageFolder(): string
    {
        return $this->storageFolder;
    }

    public function getPublicFolder(): string
    {
        return $this->publicFolder;
    }

    public function getAssetsFolder(): string
    {
        return $this->assetsFolder;
    }

    public function getAssetsDir(string $basePath): string
    {
        return sprintf('%s/%s/%s/', $this->publicFolder, $this->assetsFolder, $basePath);
    }

    public function getAssetPath(string $basePath, string $filename): string
    {
        return sprintf('%s/%s/%s', $this->assetsFolder, $basePath, $filename);
    }

    /**
     * Save a file in the filesystem from a url
     *
     * @param string $url
     * @param string $folder
     * @param string $filename
     * @throws \Exception
     */
    public function saveFromUrl(string $url, string $folder, string $filename): void
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        try {
            file_put_contents(sprintf('%s/%s', $folder, $filename), file_get_contents($url));
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error when saving file "%s". Error: %s', $filename, $e->getMessage()));

            throw $e;
        }
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
            mkdir($folder, 0777, true);
        }

        try {
            file_put_contents(sprintf('%s/%s', $folder, $filename), file_get_contents($url));
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error when saving file "%s". Error: %s', $filename, $e->getMessage()));

            throw $e;
        }
    }
}
