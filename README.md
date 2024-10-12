# Loom Video Downloader

A simple PHP package to download Loom videos.

## Installation

Install the package via composer:

```bash
composer require ianfortier/loom-downloader
```

## Usage

This package provides two main methods: `downloadVideo` to get the video content, and `saveVideo` to save the video to a file.

### Download Video Content

```php
use LoomDownloader\LoomDownloader;

$downloader = new LoomDownloader();
$videoContent = $downloader->downloadVideo('https://www.loom.com/share/your-video-id');

// Now you have the video content in $videoContent
```

### Save Video to File

```php
use LoomDownloader\LoomDownloader;

$downloader = new LoomDownloader();

// Save to a specific file
$filePath = $downloader->saveVideo('https://www.loom.com/share/your-video-id', '/path/to/save/video.mp4');

echo "Video saved to: " . $filePath;
```

### Laravel Examples

#### Save and Download

```php
use LoomDownloader\LoomDownloader;

public function downloadLoomVideo(Request $request)
{
    $downloader = new LoomDownloader();
    $filePath = $downloader->saveVideo($request->loom_url, storage_path('app/videos/loom_video.mp4'));
    
    return response()->download($filePath);
}
```

#### Stream Video Content

```php
use LoomDownloader\LoomDownloader;

public function streamLoomVideo(Request $request)
{
    $downloader = new LoomDownloader();
    $videoContent = $downloader->downloadVideo($request->loom_url);
    
    return response($videoContent)
        ->header('Content-Type', 'video/mp4')
        ->header('Content-Disposition', 'inline; filename="loom_video.mp4"');
}
```

In this example, we're using the `downloadVideo()` method to get the video content and then streaming it directly to the user's browser. The `Content-Disposition` header is set to `inline`, which will make most browsers attempt to play the video in the browser rather than download it. If you want to force a download, you can change `inline` to `attachment`.


## Testing

Run the tests with:

```bash
composer test
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
