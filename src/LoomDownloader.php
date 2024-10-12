<?php

namespace LoomDownloader;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class LoomDownloader
{
    private $client;

    private $outputDirectory;

    public function __construct(?string $outputDirectory = null)
    {
        $this->client = new Client;
        $this->outputDirectory = $outputDirectory ?? sys_get_temp_dir();
    }

    public function downloadVideo($url)
    {
        $id = $this->extractId($url);
        $downloadUrl = $this->fetchLoomDownloadUrl($id);

        return $this->fetchVideoContent($downloadUrl);
    }

    public function saveVideo($url, $destination = null)
    {
        $id = $this->extractId($url);
        $downloadUrl = $this->fetchLoomDownloadUrl($id);

        if ($destination === null) {
            $destination = $this->outputDirectory."/{$id}.mp4";
        }

        $this->ensureDirectoryExists(dirname($destination));

        return $this->saveVideoToFile($downloadUrl, $destination);
    }

    protected function fetchLoomDownloadUrl($id)
    {
        try {
            $response = $this->client->post("https://www.loom.com/api/campaigns/sessions/{$id}/transcoded-url");
            $body = json_decode($response->getBody(), true);

            return $body['url'];
        } catch (GuzzleException $e) {
            throw new \Exception('Failed to fetch Loom download URL: '.$e->getMessage());
        }
    }

    protected function fetchVideoContent($url)
    {
        try {
            $response = $this->client->get($url);

            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new \Exception('Failed to download video: '.$e->getMessage());
        }
    }

    protected function saveVideoToFile($url, $destination)
    {
        try {
            $this->client->get($url, ['sink' => $destination]);

            return $destination;
        } catch (GuzzleException $e) {
            throw new \Exception('Failed to save video: '.$e->getMessage());
        }
    }

    protected function extractId($url)
    {
        $url = explode('?', $url)[0];
        $parts = explode('/', $url);

        return end($parts);
    }

    protected function ensureDirectoryExists($directory)
    {
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }
}
