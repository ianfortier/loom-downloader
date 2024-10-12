<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use LoomDownloader\LoomDownloader;

beforeEach(function () {
    $this->mockHandler = new MockHandler;
    $handlerStack = HandlerStack::create($this->mockHandler);
    $this->mockClient = new Client(['handler' => $handlerStack]);
});

test('downloadVideo returns video content', function () {
    $this->mockHandler->append(
        new Response(200, [], json_encode(['url' => 'http://example.com/video.mp4'])),
        new Response(200, [], 'fake video content')
    );

    $downloader = new LoomDownloader;
    $reflector = new ReflectionClass($downloader);
    $clientProperty = $reflector->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($downloader, $this->mockClient);

    $content = $downloader->downloadVideo('https://www.loom.com/share/123abc');

    expect($content)->toBe('fake video content');
});

test('saveVideo saves video to file', function () {
    $this->mockHandler->append(
        new Response(200, [], json_encode(['url' => 'http://example.com/video.mp4'])),
        new Response(200, [], 'fake video content')
    );

    $downloader = new LoomDownloader;
    $reflector = new ReflectionClass($downloader);
    $clientProperty = $reflector->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($downloader, $this->mockClient);

    $tempFile = tempnam(sys_get_temp_dir(), 'loom_test_');
    $filePath = $downloader->saveVideo('https://www.loom.com/share/123abc', $tempFile);

    expect($filePath)->toBe($tempFile);
    expect(file_get_contents($tempFile))->toBe('fake video content');

    unlink($tempFile);
});

test('throws exception on API error', function () {
    $this->mockHandler->append(
        new RequestException('Error Communicating with Server', new \GuzzleHttp\Psr7\Request('GET', 'test'))
    );

    $downloader = new LoomDownloader;
    $reflector = new ReflectionClass($downloader);
    $clientProperty = $reflector->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($downloader, $this->mockClient);

    $downloader->downloadVideo('https://www.loom.com/share/123abc');
})->throws(\Exception::class, 'Failed to fetch Loom download URL');

test('throws exception on download error', function () {
    $this->mockHandler->append(
        new Response(200, [], json_encode(['url' => 'http://example.com/video.mp4'])),
        new RequestException('Error Downloading Video', new \GuzzleHttp\Psr7\Request('GET', 'test'))
    );

    $downloader = new LoomDownloader;
    $reflector = new ReflectionClass($downloader);
    $clientProperty = $reflector->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($downloader, $this->mockClient);

    $downloader->downloadVideo('https://www.loom.com/share/123abc');
})->throws(\Exception::class, 'Failed to download video');
