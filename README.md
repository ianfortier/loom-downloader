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

## 🔧 Usage Examples

The `LoomDownloader` class provides two main methods for working with Loom videos: `downloadVideo` and `saveVideo`.

### Download Video Content

To download the video content and get it as a binary:

```php
use LoomDownloader\LoomDownloader;

$downloader = new LoomDownloader();
$videoContent = $downloader->downloadVideo('https://www.loom.com/share/your-video-id');

// $videoContent now contains the binary data of the video
// Be cautious with this method for large videos as it loads the entire video into memory
```

### Save Video to File

To download the video and save it directly to a file:

```php
use LoomDownloader\LoomDownloader;

$downloader = new LoomDownloader();
$filePath = $downloader->saveVideo('https://www.loom.com/share/your-video-id', '/path/to/save/video.mp4');

echo "Video saved to: " . $filePath;
```

If you don't specify a destination, the video will be saved to a temporary directory:

```php
$filePath = $downloader->saveVideo('https://www.loom.com/share/your-video-id');
echo "Video saved to: " . $filePath;
```

## 🛠 Laravel Integration

While this package can be used in any PHP project, here are some examples of how you can integrate it with Laravel:

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

### Error Handling with Laravel Logging

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

## ℹ️ Important Information

Before using this package, please be aware of the following:

1. **Video Access**: This package attempts to download Loom videos based on their ID. It does not implement authentication, so its ability to access private videos depends on Loom's API behavior. Use caution and respect video owners' privacy settings.

2. **Error Handling**: The package throws exceptions for invalid URLs, unavailable videos, or API errors. Make sure to handle these exceptions in your code.

3. **Video Quality**: Videos are downloaded in the format provided by Loom's API. There are no options to select different qualities or formats.

4. **Large Videos**: While the package can handle large files, be cautious when using the `downloadVideo` method as it loads the entire video into memory. For large videos, prefer the `saveVideo` method which streams the video directly to a file.

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
