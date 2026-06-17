<?php

namespace App\Services;

use App\Models\InstagramFeed;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class InstagramService
{
    protected $accessToken;
    protected $userId;

    public function __construct()
    {
        $this->accessToken = config('services.instagram.access_token');
        $this->userId = config('services.instagram.user_id');
    }

    public function getRecentMedia($limit = 12)
    {
        return Cache::remember('instagram_feeds', 3600, function () use ($limit) {
            try {
                if (!$this->accessToken || !$this->userId) {
                    Log::warning('Instagram credentials not configured');
                    return $this->getFromDatabase($limit);
                }

                $response = Http::timeout(10)->get("https://graph.instagram.com/{$this->userId}/media", [
                    'fields' => 'id,media_url,permalink,caption,like_count,timestamp,media_type',
                    'access_token' => $this->accessToken,
                    'limit' => $limit,
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['data']) && is_array($data['data'])) {
                        $this->saveToDatabase($data['data']);
                        return $data['data'];
                    }
                } else {
                    Log::error('Instagram API Error: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('Instagram Service Exception: ' . $e->getMessage());
            }

            return $this->getFromDatabase($limit);
        });
    }

    protected function saveToDatabase($items)
    {
        foreach ($items as $item) {
            // Only save images and carousels, skip videos
            if (in_array($item['media_type'], ['IMAGE', 'CAROUSEL_ALBUM'])) {
                InstagramFeed::updateOrCreate(
                    ['media_id' => $item['id']],
                    [
                        'media_url' => $item['media_url'],
                        'permalink' => $item['permalink'],
                        'caption' => $item['caption'] ?? null,
                        'like_count' => $item['like_count'] ?? 0,
                        'timestamp' => date('Y-m-d H:i:s', strtotime($item['timestamp'])),
                    ]
                );
            }
        }
    }

    protected function getFromDatabase($limit)
    {
        $feeds = InstagramFeed::recent($limit)->get();

        // Convert to array format similar to API response
        return $feeds->map(function($feed) {
            return [
                'id' => $feed->media_id,
                'media_url' => $feed->media_url,
                'permalink' => $feed->permalink,
                'caption' => $feed->caption,
                'like_count' => $feed->like_count,
                'timestamp' => $feed->timestamp->toIso8601String(),
                'media_type' => 'IMAGE',
            ];
        })->toArray();
    }

    public function refreshToken()
    {
        // For Instagram Basic Display API, long-lived token refresh
        // This requires the refresh_token which we don't store by default
        // Implement if you have refresh_token saved
        try {
            $refreshToken = Cache::get('instagram_refresh_token');
            if (!$refreshToken) {
                return false;
            }

            $response = Http::get('https://graph.instagram.com/refresh_access_token', [
                'grant_type' => 'ig_refresh_token',
                'access_token' => $this->accessToken,
            ]);

            if ($response->successful()) {
                $newToken = $response->json()['access_token'];
                // Update .env or config with new token
                return $newToken;
            }
        } catch (\Exception $e) {
            Log::error('Token refresh failed: ' . $e->getMessage());
        }

        return false;
    }

    public function getUserInfo()
    {
        try {
            $response = Http::get("https://graph.instagram.com/{$this->userId}", [
                'fields' => 'id,username,account_type,media_count',
                'access_token' => $this->accessToken,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Get user info failed: ' . $e->getMessage());
        }

        return null;
    }
}
