# Loom Video Downloader

A simple PHP package to download Loom videos with ease.

## 🚀 Installation

Install the package via Composer:

```bash
composer require ianfortier/loom-downloader
```

## 🤔 Why?

Loom doesn't provide an official API for video downloads. This package fills that gap, allowing you to:
- Create backups of your Loom videos
- Archive old content
- Have offline access to your videos

Use responsibly and only for videos you have the right to download and store.

## 🎬 Quick Start

Here's a quick example to get you started:

```php
use LoomDownloader\LoomDownloader;

$downloader = new LoomDownloader();
$filePath = $downloader->saveVideo('https://www.loom.com/share/your-video-id', 'path/to/save/video.mp4');

echo "Video saved to: " . $filePath;
```

## 🔧 Usage Examples

### Download Video Content

To get the video content as a string:

```php
$downloader = new LoomDownloader();
$videoContent = $downloader->downloadVideo('https://www.loom.com/share/your-video-id');
```

### Save Video to File

To save the video directly to a file:

```php
$downloader = new LoomDownloader();
$filePath = $downloader->saveVideo('https://www.loom.com/share/your-video-id', '/path/to/save/video.mp4');
```

## 🛠 Laravel Integration

While this package can be used in any PHP project, it integrates smoothly with Laravel. Here are some examples of how you can use the Loom Video Downloader in your Laravel application:

### Basic Usage in a Controller

```php
use LoomDownloader\LoomDownloader;

class LoomController extends Controller
{
    public function download(Request $request)
    {
        $downloader = new LoomDownloader();
        $filePath = $downloader->saveVideo($request->loom_url, storage_path('app/videos/loom_video.mp4'));
        
        return response()->download($filePath);
    }

    public function stream(Request $request)
    {
        $downloader = new LoomDownloader();
        $videoContent = $downloader->downloadVideo($request->loom_url);
        
        return response($videoContent)
            ->header('Content-Type', 'video/mp4')
            ->header('Content-Disposition', 'inline; filename="loom_video.mp4"');
    }
}
```

### Integration with Laravel's File Storage

Use Laravel's `Storage` facade to save the downloaded video:

```php
use Illuminate\Support\Facades\Storage;
use LoomDownloader\LoomDownloader;

class LoomController extends Controller
{
    public function store(Request $request)
    {
        $downloader = new LoomDownloader();
        $videoContent = $downloader->downloadVideo($request->loom_url);
        
        $path = Storage::put('videos/loom-video.mp4', $videoContent);
        
        return response()->json(['message' => 'Video stored successfully', 'path' => $path]);
    }
}
```

### Background Processing with Laravel Queues

Create a job to download videos in the background:

```php
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LoomDownloader\LoomDownloader;

class DownloadLoomVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $loomUrl;
    protected $savePath;

    public function __construct($loomUrl, $savePath)
    {
        $this->loomUrl = $loomUrl;
        $this->savePath = $savePath;
    }

    public function handle()
    {
        $downloader = new LoomDownloader();
        $downloader->saveVideo($this->loomUrl, $this->savePath);
    }
}

// In your controller:
DownloadLoomVideo::dispatch($loomUrl, storage_path('app/videos/loom_video.mp4'));
```

### Error Handling with Laravel Logging

Wrap the download process in a try-catch block and use Laravel's logging:

```php
use Illuminate\Support\Facades\Log;
use LoomDownloader\LoomDownloader;

class LoomController extends Controller
{
    public function download(Request $request)
    {
        try {
            $downloader = new LoomDownloader();
            $filePath = $downloader->saveVideo($request->loom_url, storage_path('app/videos/loom_video.mp4'));
            return response()->download($filePath);
        } catch (\Exception $e) {
            Log::error('Failed to download Loom video: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to download video'], 500);
        }
    }
}
```

### Caching Downloaded Videos

Use Laravel's Cache facade to store downloaded videos:

```php
use Illuminate\Support\Facades\Cache;
use LoomDownloader\LoomDownloader;

class LoomController extends Controller
{
    public function getCachedVideo(Request $request)
    {
        $videoId = $request->video_id;
        $videoContent = Cache::remember('loom_video_' . $videoId, 3600, function () use ($request) {
            $downloader = new LoomDownloader();
            return $downloader->downloadVideo($request->loom_url);
        });

        return response($videoContent)
            ->header('Content-Type', 'video/mp4')
            ->header('Content-Disposition', 'inline; filename="loom_video.mp4"');
    }
}
```

## ℹ️ Important Information

Before using this package, please be aware of the following:

1. **Video Access**: This package attempts to download Loom videos based on their ID. It does not implement authentication, so its ability to access private videos depends on Loom's API behavior. Use caution and respect video owners' privacy settings.

2. **Error Handling**: The package throws exceptions for invalid URLs, unavailable videos, or API errors. Make sure to handle these exceptions in your code.

3. **Video Quality**: Videos are downloaded in the format provided by Loom's API. There are no options to select different qualities or formats.

4. **Large Videos**: While the package can handle large files, there are no specific optimizations or progress indicators for lengthy downloads.

5. **Legal and Ethical Considerations**: Ensure you have the right to download and use the Loom videos. This package does not enforce access controls or address legal concerns related to video downloading. Always respect copyright and privacy rights.

## 🧪 Testing

Run the tests with:

```bash
composer test
```

## 📄 License

This package is open-source software licensed under the [MIT license](LICENSE.md).

---

For more detailed information or to report issues, please visit our [GitHub repository](https://github.com/ianfortier/loom-downloader).
