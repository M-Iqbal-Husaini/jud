<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Dataset;
use App\Models\DatasetItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class ScrapeYoutubeCommentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour
    public $tries = 3;

    protected int $datasetId;
    protected string $batchId;
    protected string $sourceType;
    protected string $sourceValue;
    protected int $maxCommentsPerVideo;
    protected bool $includeReplies;
    protected array $keywords;

    public function __construct(
        int $datasetId,
        string $batchId,
        string $sourceType,
        string $sourceValue,
        int $maxCommentsPerVideo = 200,
        bool $includeReplies = true,
        array $keywords = []
    ) {
        $this->datasetId = $datasetId;
        $this->batchId = $batchId;
        $this->sourceType = $sourceType;
        $this->sourceValue = $sourceValue;
        $this->maxCommentsPerVideo = $maxCommentsPerVideo;
        $this->includeReplies = $includeReplies;
        $this->keywords = $keywords;
    }

    public function handle(): void
    {
        try {
            $this->updateStatus('processing', 0, 'Memulai scraping...');

            $dataset = Dataset::findOrFail($this->datasetId);

            // Parse video URLs (satu per baris)
            $videoUrls = array_filter(array_map('trim', explode("\n", $this->sourceValue)));
            if (empty($videoUrls)) {
                throw new \Exception('Tidak ada URL video yang valid');
            }

            $totalVideos = count($videoUrls);
            $processedVideos = 0;
            $totalComments = 0;

            foreach ($videoUrls as $videoUrl) {
                try {
                    $videoId = $this->extractVideoId($videoUrl);
                    if (!$videoId) {
                        Log::warning("Invalid video URL: {$videoUrl}");
                        $processedVideos++;
                        continue;
                    }

                    $this->updateStatus(
                        'processing',
                        (int)(($processedVideos / $totalVideos) * 100),
                        "Scraping video " . ($processedVideos+1) . "/{$totalVideos}: {$videoId}"
                    );

                    $comments = $this->scrapeVideoComments($videoId);

                    foreach ($comments as $comment) {
                        $this->saveComment($dataset, $comment, $videoId);
                        $totalComments++;
                    }

                    $processedVideos++;

                } catch (\Throwable $e) {
                    Log::error("Error scraping video {$videoUrl}: " . $e->getMessage());
                    $processedVideos++;
                    continue;
                }
            }

            // update dataset rows count (defensive)
            $dataset->update([
                'rows' => DatasetItem::where('dataset_id', $dataset->id)->count()
            ]);

            $this->updateStatus('done', 100, "Selesai! Total {$totalComments} komentar dari {$totalVideos} video");

        } catch (\Throwable $e) {
            Log::error('ScrapeYoutubeCommentsJob failed: ' . $e->getMessage());
            $this->updateStatus('failed', 0, 'Error: ' . $e->getMessage());
            // rethrow kalau ingin agar mekanisme queue menandai gagal
            throw $e;
        }
    }

    protected function extractVideoId(string $url): ?string
    {
        $patterns = [
            '/youtube\.com\/watch\?v=([^&]+)/',
            '/youtu\.be\/([^?]+)/',
            '/youtube\.com\/embed\/([^?]+)/',
            '/youtube\.com\/v\/([^?]+)/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        return null;
    }

    protected function scrapeVideoComments(string $videoId): array
    {
        $apiKey = config('services.youtube.api_key');

        if (!$apiKey) {
            Log::warning('YouTube API key not configured, returning mock comments');
            return $this->getMockComments($videoId);
        }

        $comments = [];
        $pageToken = null;
        $fetchedCount = 0;

        do {
            try {
                $url = 'https://www.googleapis.com/youtube/v3/commentThreads';
                $params = [
                    'part' => 'snippet',
                    'videoId' => $videoId,
                    'maxResults' => min(100, $this->maxCommentsPerVideo - $fetchedCount),
                    'key' => $apiKey,
                    'order' => 'relevance'
                ];

                if ($pageToken) {
                    $params['pageToken'] = $pageToken;
                }

                $response = Http::get($url, $params);

                if (!$response->successful()) {
                    Log::error("YouTube API error for video {$videoId}: " . $response->body());
                    break;
                }

                $data = $response->json();

                if (!isset($data['items'])) {
                    break;
                }

                foreach ($data['items'] as $item) {
                    $snippet = $item['snippet']['topLevelComment']['snippet'];

                    $comments[] = [
                        'comment_id' => $item['snippet']['topLevelComment']['id'],
                        'text' => $snippet['textDisplay'],
                        'author' => $snippet['authorDisplayName'],
                        'published_at' => $snippet['publishedAt'],
                        'like_count' => $snippet['likeCount'] ?? 0,
                    ];

                    $fetchedCount++;

                    if ($this->includeReplies && isset($item['replies']['comments'])) {
                        foreach ($item['replies']['comments'] as $reply) {
                            $replySnippet = $reply['snippet'];
                            $comments[] = [
                                'comment_id' => $reply['id'],
                                'text' => $replySnippet['textDisplay'],
                                'author' => $replySnippet['authorDisplayName'],
                                'published_at' => $replySnippet['publishedAt'],
                                'like_count' => $replySnippet['likeCount'] ?? 0,
                            ];
                            $fetchedCount++;
                            if ($fetchedCount >= $this->maxCommentsPerVideo) break 2;
                        }
                    }

                    if ($fetchedCount >= $this->maxCommentsPerVideo) {
                        break 2;
                    }
                }

                $pageToken = $data['nextPageToken'] ?? null;

            } catch (\Throwable $e) {
                Log::error("Error fetching comments for video {$videoId}: " . $e->getMessage());
                break;
            }

        } while ($pageToken && $fetchedCount < $this->maxCommentsPerVideo);

        return $comments;
    }

    protected function getMockComments(string $videoId): array
    {
        $mock = [
            'Video bagus banget! Sangat membantu',
            'Terima kasih atas informasinya',
            'Mantap jiwa! Keep up the good work',
            'Konten berkualitas, subscribe!',
            'Penjelasannya mudah dipahami',
            'Wow amazing content!',
            'Thanks for sharing this',
            'Very informative video',
            'Keep it up!',
            'Great explanation',
        ];

        $res = [];
        $count = min(10, $this->maxCommentsPerVideo);
        for ($i = 0; $i < $count; $i++) {
            $res[] = [
                'comment_id' => $videoId . '_mock_' . $i,
                'text' => $mock[$i % count($mock)],
                'author' => 'Mock User ' . ($i + 1),
                'published_at' => now()->subDays(rand(1, 30))->toIso8601String(),
                'like_count' => rand(0, 100),
            ];
        }
        return $res;
    }

    /**
     * Save comment to database safely: only include fields if column exists.
     */
    protected function saveComment(Dataset $dataset, array $comment, string $videoId): void
    {
        $exists = DatasetItem::where('comment_id', $comment['comment_id'])->exists();
        if ($exists) return;

        $data = [
            'dataset_id' => $dataset->id,
            'comment_id' => $comment['comment_id'],
            'text' => strip_tags($comment['text']),
        ];

        // Optional columns only if present in DB
        if (Schema::hasColumn('dataset_items', 'video_id')) {
            $data['video_id'] = $videoId;
        }
        if (Schema::hasColumn('dataset_items', 'author')) {
            $data['author'] = $comment['author'] ?? null;
        }
        if (Schema::hasColumn('dataset_items', 'published_at')) {
            $data['published_at'] = $comment['published_at'] ?? null;
        }
        if (Schema::hasColumn('dataset_items', 'like_count')) {
            $data['like_count'] = $comment['like_count'] ?? 0;
        }
        if (Schema::hasColumn('dataset_items', 'label')) {
            $data['label'] = null;
        }
        if (Schema::hasColumn('dataset_items', 'label_auto')) {
            $data['label_auto'] = false;
        }
        if (Schema::hasColumn('dataset_items', 'batch_id')) {
            $data['batch_id'] = $this->batchId;
        }

        DatasetItem::create($data);
    }

    protected function updateStatus(string $status, int $progress, string $message): void
    {
        Cache::put("scrape:{$this->batchId}", [
            'status' => $status,
            'progress' => $progress,
            'message' => $message,
        ], 3600);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ScrapeYoutubeCommentsJob failed permanently: ' . $exception->getMessage());
        $this->updateStatus('failed', 0, 'Job gagal: ' . $exception->getMessage());
    }
}
