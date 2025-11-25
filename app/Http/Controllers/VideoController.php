<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class VideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the list of YouTube videos.
     * Ini adalah metode 'index' yang hilang dan menyebabkan error.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $videos = [];
        
        if ($user->youtube_access_token) {
            try {
                // Refresh token if needed
                $this->refreshTokenIfNeeded($user);
                
                // Get videos from user's channel
                $videos = $this->getUserChannelVideos($user);
                
            } catch (\Exception $e) {
                Log::error('Error fetching YouTube videos: ' . $e->getMessage());
                session()->flash('warning', 'Failed to fetch YouTube videos. Please try reconnecting your account.');
                $videos = $this->getDummyVideos();
            }
        } else {
            session()->flash('info', 'Please connect your YouTube account to view your videos.');
            return view('videos.index', ['videos' => []]);
        }

        if (empty($videos)) {
            session()->flash('info', 'No videos found in your YouTube channel. Make sure your channel has public videos.');
        }

        return view('videos.index', compact('videos'));
    }

    private function refreshTokenIfNeeded($user)
    {
        // Check if token is expired or will expire soon (within 5 minutes)
        if ($user->youtube_token_expires_at && $user->youtube_token_expires_at->subMinutes(5)->isPast()) {
            if ($user->youtube_refresh_token) {
                $this->refreshAccessToken($user);
            } else {
                throw new \Exception('YouTube token expired and no refresh token available. Please reconnect your account.');
            }
        }
    }

    private function refreshAccessToken($user)
    {
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'refresh_token' => $user->youtube_refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            $user->update([
                'youtube_access_token' => $data['access_token'],
                'youtube_token_expires_at' => now()->addSeconds($data['expires_in']),
            ]);
            
            Log::info('YouTube access token refreshed for user: ' . $user->id);
        } else {
            Log::error('Failed to refresh YouTube token: ' . $response->body());
            throw new \Exception('Failed to refresh YouTube access token');
        }
    }

    private function getUserChannelVideos($user)
    {
        $videos = [];
        
        // Get user's channel info
        $channelResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $user->youtube_access_token,
        ])->get('https://www.googleapis.com/youtube/v3/channels', [
            'part' => 'id,snippet,contentDetails',
            'mine' => 'true'
        ]);

        if (!$channelResponse->successful()) {
            Log::error('Failed to get channel info: ' . $channelResponse->body());
            throw new \Exception('Failed to get YouTube channel information');
        }

        $channelData = $channelResponse->json();
        
        if (empty($channelData['items'])) {
            Log::warning('No YouTube channel found for user: ' . $user->id);
            return [];
        }

        $channel = $channelData['items'][0];
        $uploadsPlaylistId = $channel['contentDetails']['relatedPlaylists']['uploads'];
        
        Log::info('Found channel: ' . $channel['snippet']['title'] . ' with uploads playlist: ' . $uploadsPlaylistId);

        // Get videos from uploads playlist
        $videos = $this->getPlaylistVideos($uploadsPlaylistId, $user);
        
        return $videos;
    }

    private function getPlaylistVideos($playlistId, $user)
    {
        $videos = [];
        $nextPageToken = null;
        $maxResults = 50;
        $totalFetched = 0;
        $maxVideos = 200; // Limit to prevent too many API calls
        
        do {
            $params = [
                'part' => 'snippet',
                'playlistId' => $playlistId,
                'maxResults' => $maxResults,
            ];
            
            if ($nextPageToken) {
                $params['pageToken'] = $nextPageToken;
            }

            $playlistResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $user->youtube_access_token,
            ])->get('https://www.googleapis.com/youtube/v3/playlistItems', $params);

            if (!$playlistResponse->successful()) {
                Log::error('Failed to get playlist items: ' . $playlistResponse->body());
                break;
            }

            $playlistData = $playlistResponse->json();
            $playlistItems = $playlistData['items'] ?? [];
            
            if (empty($playlistItems)) {
                break;
            }

            // Extract video IDs
            $videoIds = [];
            foreach ($playlistItems as $item) {
                if (isset($item['snippet']['resourceId']['videoId'])) {
                    $videoIds[] = $item['snippet']['resourceId']['videoId'];
                }
            }

            if (!empty($videoIds)) {
                // Get detailed video information and run detection
                $videoDetails = $this->getVideoDetails($videoIds, $user);
                $videos = array_merge($videos, $videoDetails);
            }

            $nextPageToken = $playlistData['nextPageToken'] ?? null;
            $totalFetched += count($playlistItems);
            
        } while ($nextPageToken && $totalFetched < $maxVideos);
        
        Log::info('Fetched ' . count($videos) . ' videos from YouTube');
        
        return $videos;
    }

    private function getVideoDetails($videoIds, $user)
    {
        $videos = [];
        
        // Process in chunks of 50 (YouTube API limit)
        $chunks = array_chunk($videoIds, 50);
        
        foreach ($chunks as $chunk) {
            $videoIdsString = implode(',', $chunk);
            
            $detailsResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $user->youtube_access_token,
            ])->get('https://www.googleapis.com/youtube/v3/videos', [
                'part' => 'snippet,contentDetails,statistics',
                'id' => $videoIdsString,
            ]);

            if ($detailsResponse->successful()) {
                $videoData = $detailsResponse->json();
                
                foreach ($videoData['items'] ?? [] as $video) {
                    $videoId = $video['id'];
                    $title = $video['snippet']['title'];
                    $description = $video['snippet']['description'] ?? '';

                    // Panggil fungsi deteksi di sini untuk SETIAP video
                    $detectionResult = $this->runPythonDetection($title, $description);

                    $videos[] = [
                        'id' => $videoId,
                        'title' => $title,
                        'description' => $description,
                        'thumbnail' => $this->getBestThumbnail($video['snippet']['thumbnails']),
                        'duration' => $this->formatDuration($video['contentDetails']['duration'] ?? 'PT0S'),
                        'views' => intval($video['statistics']['viewCount'] ?? 0),
                        'upload_date' => $video['snippet']['publishedAt'],
                        'category' => $video['snippet']['categoryId'] ?? 'YouTube',
                        'author' => $video['snippet']['channelTitle'] ?? $user->name,
                        'youtube_url' => "https://www.youtube.com/watch?v={$videoId}",
                        'detection_status' => $detectionResult,
                    ];
                }
            } else {
                Log::error('Failed to get video details for chunk: ' . $videoIdsString . ' Error: ' . $detailsResponse->body());
            }
        }
        
        return $videos;
    }

    private function getBestThumbnail($thumbnails)
    {
        // Prefer higher quality thumbnails
        if (isset($thumbnails['maxres']['url'])) {
            return $thumbnails['maxres']['url'];
        }
        if (isset($thumbnails['high']['url'])) {
            return $thumbnails['high']['url'];
        }
        if (isset($thumbnails['medium']['url'])) {
            return $thumbnails['medium']['url'];
        }
        if (isset($thumbnails['default']['url'])) {
            return $thumbnails['default']['url'];
        }
        
        return 'https://via.placeholder.com/480x360/4F46E5/FFFFFF?text=No+Thumbnail';
    }

    private function formatDuration($duration)
    {
        try {
            $interval = new \DateInterval($duration);
            
            $hours = $interval->h;
            $minutes = $interval->i;
            $seconds = $interval->s;
            
            if ($hours > 0) {
                return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
            } else {
                return sprintf('%d:%02d', $minutes, $seconds);
            }
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    public function show($id)
    {
        return redirect("https://www.youtube.com/watch?v={$id}");
    }

    public function connectYoutube()
    {
        return redirect()->route('auth.google');
    }

    private function getDummyVideos()
    {
        return [
            [
                'id' => 'demo1',
                'title' => 'Connect Your YouTube Account',
                'description' => 'To see your actual YouTube videos here, please connect your YouTube account through Google login.',
                'thumbnail' => 'https://via.placeholder.com/480x360/4F46E5/FFFFFF?text=Connect+YouTube',
                'duration' => '0:00',
                'views' => 0,
                'upload_date' => now()->toISOString(),
                'category' => 'Info',
                'author' => 'System',
                'youtube_url' => '#'
            ]
        ];
    }

    /**
     * Mendeteksi komentar promosi judi online pada video tertentu.
     * Dipanggil melalui AJAX dari frontend.
     *
     * @param string $videoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function detectComments(Request $request, string $videoId)
    {
        try {
            $user = $request->user();

            if (!$user || !$user->youtube_access_token) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Akun YouTube belum terhubung.',
                ], 400);
            }

            // 1) Ambil komentar dari YouTube API (top-level comments)
            $youtubeToken = $user->youtube_access_token;

            $youtubeResponse = Http::withToken($youtubeToken)
                ->get('https://www.googleapis.com/youtube/v3/commentThreads', [
                    'part'       => 'snippet',
                    'videoId'    => $videoId,
                    'maxResults' => 100,      // bisa dinaikkan kalau mau
                    'order'      => 'time',
                    'textFormat' => 'plainText',
                ]);

            if (!$youtubeResponse->ok()) {
                Log::warning('YouTube API error', [
                    'status' => $youtubeResponse->status(),
                    'body'   => $youtubeResponse->body(),
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal mengambil komentar dari YouTube (status '.$youtubeResponse->status().').',
                ], 500);
            }

            $ytData   = $youtubeResponse->json();
            $items    = $ytData['items'] ?? [];
            $comments = [];

            foreach ($items as $item) {
                $topLevel = $item['snippet']['topLevelComment'] ?? null;
                $snippet  = $topLevel['snippet'] ?? null;

                if (!$snippet) {
                    continue;
                }

                $text = $snippet['textDisplay'] ?? $snippet['textOriginal'] ?? '';
                $text = strip_tags($text ?? '');

                if (trim($text) === '') {
                    continue;
                }

                $comments[] = [
                    // ID thread komentar (bisa dipakai untuk commentThreads.delete)
                    'thread_id'   => $item['id'] ?? null,
                    // ID komentar-nya sendiri (bisa dipakai untuk comments.delete)
                    'comment_id'  => $topLevel['id'] ?? null,
                    'author'      => $snippet['authorDisplayName'] ?? 'Anonim',
                    'text'        => $text,
                    'publishedAt' => $snippet['publishedAt'] ?? null,
                ];
            }

            // 2) Kirim komentar ke FastAPI (batch)
            $fastapiUrl = rtrim(env('FASTAPI_URL', 'http://127.0.0.1:8001'), '/') . '/predict/batch';

            $texts = array_map(fn ($c) => $c['text'], $comments);

            $mlResponse = Http::timeout(60)->post($fastapiUrl, [
                'texts' => $texts,
            ]);

            if (!$mlResponse->ok()) {
                Log::error('FastAPI batch predict error', [
                    'status' => $mlResponse->status(),
                    'body'   => $mlResponse->body(),
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal memproses komentar di layanan ML (status '.$mlResponse->status().').',
                ], 500);
            }

            $mlData   = $mlResponse->json();
            $results  = $mlData['results'] ?? [];
            $detected = [];

            // Asumsi urutan results sama dengan urutan texts
            foreach ($results as $idx => $res) {
                $label      = (int)($res['label'] ?? 0);
                $prediction = (float)($res['prediction'] ?? 0.0);

                if (!isset($comments[$idx])) {
                    continue;
                }

                if ($label === 1) { // 1 = judi
                    $c = $comments[$idx];

                    $detected[] = [
                        'thread_id'        => $c['thread_id'] ?? null,
                        'comment_id'       => $c['comment_id'] ?? null,
                        'author'           => $c['author'],
                        'text'             => $c['text'],
                        'publishedAt'      => $c['publishedAt'],
                        'prediction_score' => $prediction,
                        'label'            => $label,
                        'detection_status' => 'Komentar Judi Online',
                    ];
                }
            }

            return response()->json([
                'status'            => 'success',
                'video_id'          => $videoId,
                'total_comments'    => count($comments),
                'detected_count'    => count($detected),
                'detected_comments' => $detected,
            ]);
        } catch (\Throwable $e) {
            Log::error('detectComments exception: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server saat mendeteksi komentar.',
            ], 500);
        }
    }


    /**
     * Mengambil komentar publik untuk video dari YouTube Data API.
     *
     * @param string $videoId
     * @param string $accessToken
     * @return array
     * @throws \Exception
     */
    private function getPublicCommentsForVideo($videoId, $accessToken)
    {
        $comments = [];
        $nextPageToken = null;
        $maxResults = 100; // Maksimal 100 komentar per request

        do {
            $params = [
                'part' => 'snippet', // Untuk mendapatkan teks komentar dan detail lainnya
                'videoId' => $videoId,
                'maxResults' => $maxResults,
                'textFormat' => 'plainText' // Format teks komentar
            ];

            if ($nextPageToken) {
                $params['pageToken'] = $nextPageToken;
            }

            // Endpoint untuk commentThreads (top-level comments)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json'
            ])->get('https://www.googleapis.com/youtube/v3/commentThreads', $params);

            if (!$response->successful()) {
                Log::error('Failed to get comments for video ' . $videoId . ': ' . $response->body());
                throw new \Exception('Failed to fetch comments from YouTube API: ' . $response->json()['error']['message'] ?? 'Unknown error');
            }

            $commentData = $response->json();
            $items = $commentData['items'] ?? [];

            foreach ($items as $item) {
                if (isset($item['snippet']['topLevelComment']['snippet'])) {
                    $snippet = $item['snippet']['topLevelComment']['snippet'];
                    $comments[] = [
                        'id' => $item['id'],
                        'authorDisplayName' => $snippet['authorDisplayName'],
                        'textDisplay' => $snippet['textDisplay'],
                        'publishedAt' => $snippet['publishedAt']
                    ];
                }
            }

            $nextPageToken = $commentData['nextPageToken'] ?? null;

            // Batasi jumlah komentar untuk menghindari kuota API berlebihan saat testing
            // Anda bisa hapus atau tingkatkan ini di produksi
            if (count($comments) >= 200) { // Misal, hanya ambil 200 komentar teratas
                break;
            }

        } while ($nextPageToken);

        Log::info('Fetched ' . count($comments) . ' comments for video ' . $videoId);

        return $comments;
    }

    // Ubah parameter runPythonDetection agar menerima teks komentar
    private function runPythonDetection($author, $commentText)
    {
        $pythonScriptPath = storage_path('app/python/run_detection.py');
        // Sesuaikan path Python Venv sesuai OS Anda
        // Untuk Linux/macOS:
        // $venvPythonPath = base_path('venv_detection/bin/python');
        // Untuk Windows:
        $venvPythonPath = base_path('venv_detection\\Scripts\\python.exe'); // Dikoreksi kembali ke venv_detection

        $process = new Process([
            $venvPythonPath,
            $pythonScriptPath,
            json_encode(['comment_text' => $commentText]) // Mengirim teks komentar sebagai JSON string
        ]);

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $output = trim($process->getOutput());
            Log::info('Python detection output for comment: "' . substr($commentText, 0, 50) . '...": ' . $output);

            $result = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($result['prediction'])) {
                Log::error('Invalid JSON output from Python script: ' . $output);
                return 'Deteksi Gagal (Format Output Invalid)';
            }

            return $result['prediction'];
        } catch (ProcessFailedException $exception) {
            Log::error('Python detection failed: ' . $exception->getMessage() . ' Error output: ' . $process->getErrorOutput());
            return 'Deteksi Gagal (Error Eksekusi Skrip)';
        } catch (\Exception $e) {
            Log::error('Error running Python detection: ' . $e->getMessage());
            return 'Deteksi Gagal (Kesalahan Umum)';
        }
    }

    public function deleteDetectedComments(Request $request, $videoId)
    {
        $data = $request->validate([
            'comments'   => 'required|array|min:1',
            'comments.*' => 'string'
        ]);

        $user = $request->user();
        if (!$user || !$user->youtube_access_token) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun YouTube belum terhubung.'
            ], 400);
        }

        $accessToken = $user->youtube_access_token;
        $baseUrl     = 'https://www.googleapis.com/youtube/v3/comments/setModerationStatus';

        $deleted = 0;
        $failed  = [];

        foreach ($data['comments'] as $commentId) {
            // sama seperti curl yg berhasil:
            // curl -X POST "https://www.googleapis.com/youtube/v3/comments/setModerationStatus?id=XXX&moderationStatus=rejected" -d ""
            $url = $baseUrl . '?id=' . urlencode($commentId) . '&moderationStatus=rejected';

            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                // body = string kosong, BUKAN array (supaya tidak di-JSON-kan)
                ->post($url, '');

            if ($response->status() === 204) {
                $deleted++;
            } else {
                $failed[] = [
                    'id'     => $commentId,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ];
            }
        }

        if (!empty($failed)) {
            Log::warning('Some comment moderation failed', $failed);
        }

        return response()->json([
            'status'        => 'success',
            'deleted_count' => $deleted,
            'failed_count'  => count($failed),
            'failed'        => $failed,
            'message'       => "Berhasil menghapus {$deleted} komentar (gagal: " . count($failed) . ")",
        ]);
    }





}