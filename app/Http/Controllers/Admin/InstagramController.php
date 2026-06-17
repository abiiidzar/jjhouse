<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InstagramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class InstagramController extends Controller
{
    public function index()
    {
        $accessToken = config('services.instagram.access_token');
        $userId = config('services.instagram.user_id');
        return view('admin.instagram.index', compact('accessToken', 'userId'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
            'user_id' => 'required|string',
        ]);

        $this->updateEnv([
            'INSTAGRAM_ACCESS_TOKEN' => $request->access_token,
            'INSTAGRAM_USER_ID' => $request->user_id,
        ]);

        Cache::forget('instagram_feeds');

        return redirect()->back()->with('success', 'Instagram settings saved successfully.');
    }

    public function refresh()
    {
        Cache::forget('instagram_feeds');

        $instagram = new InstagramService();
        $instagram->getRecentMedia(12);

        return redirect()->back()->with('success', 'Feed cache refreshed successfully.');
    }

    public function test()
    {
        $instagram = new InstagramService();
        $feeds = $instagram->getRecentMedia(6);

        if (empty($feeds)) {
            return redirect()->back()->with('error', 'Failed to fetch Instagram feed. Please check your token.');
        }

        return redirect()->back()->with('success', 'Instagram feed works! ' . count($feeds) . ' posts fetched.');
    }

    private function updateEnv($data)
    {
        $path = base_path('.env');

        if (!File::exists($path)) {
            return false;
        }

        $content = File::get($path);

        foreach ($data as $key => $value) {
            if (str_contains($content, $key . '=')) {
                $content = preg_replace('/' . $key . '=.*/', $key . '=' . $value, $content);
            } else {
                $content .= "\n" . $key . '=' . $value;
            }
        }

        File::put($path, $content);

        return true;
    }
}
