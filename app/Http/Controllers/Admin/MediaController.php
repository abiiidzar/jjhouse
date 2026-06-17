<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Show media management page
     */
    public function index()
    {
        $homeImages = [
            'hero_image_1' => Setting::get('home_hero_image_1') ?? '/images/hero.jpg',
            'hero_image_2' => Setting::get('home_hero_image_2') ?? '/images/hero.jpg',
            'hero_image_3' => Setting::get('home_hero_image_3') ?? '/images/hero.jpg',

            'story_image' => Setting::get('home_story_image') ?? '/images/story.jpg',
            'editorial_image_1' => Setting::get('home_editorial_image_1') ?? '/images/editorial1.jpg',
            'editorial_image_2' => Setting::get('home_editorial_image_2') ?? '/images/editorial2.jpg',
            'editorial_image_3' => Setting::get('home_editorial_image_3') ?? '/images/editorial3.jpg',
        ];

        // Get home page visibility settings
        $homeSettings = [
            'show_story_section' => Setting::get('home_show_story_section', true),
            'show_featured_products' => Setting::get('home_show_featured_products', true),
            'show_editorial_grid' => Setting::get('home_show_editorial_grid', true),
            'featured_products_count' => Setting::get('home_featured_products_count', 8),
        ];

        return view('admin.media.index', compact('homeImages', 'homeSettings'));
    }

    /**
     * Upload home hero image
     */
    public function uploadHeroImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'position' => 'required|in:1,2,3',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('home', $filename, 'public');

            $position = $request->input('position');

            Setting::set('home_hero_image_' . $position, '/storage/home/' . $filename);

            return back()->with('success', 'Hero slide ' . $position . ' updated');
        }

        return back()->with('error', 'Upload gagal');
    }

    /**
     * Upload editorial images
     */
    public function uploadEditorialImage(Request $request)
    {
        $request->validate([
            'editorial_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'position' => 'required|in:1,2,3',
        ]);

        if ($request->hasFile('editorial_image')) {
            $file = $request->file('editorial_image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('home', $filename, 'public');

            $position = $request->input('position');
            Setting::set('home_editorial_image_' . $position, '/storage/home/' . $filename);

            return back()->with('success', 'Editorial image ' . $position . ' updated successfully');
        }

        return back()->with('error', 'Failed to upload editorial image');
    }

    /**
     * Upload story image
     */
    public function uploadStoryImage(Request $request)
    {
        $request->validate([
            'story_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('story_image')) {
            $file = $request->file('story_image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('home', $filename, 'public');

            Setting::set('home_story_image', '/storage/home/' . $filename);

            return back()->with('success', 'Story image updated successfully');
        }

        return back()->with('error', 'Failed to upload story image');
    }

    /**
     * Delete home image and reset to default
     */
    public function resetImage(Request $request)
    {
        $request->validate([
        'hero_1' => ['key' => 'home_hero_image_1', 'default' => '/images/hero.jpg'],
        'hero_2' => ['key' => 'home_hero_image_2', 'default' => '/images/hero.jpg'],
        'hero_3' => ['key' => 'home_hero_image_3', 'default' => '/images/hero.jpg'],
        ]);

        $imageType = $request->input('image_type');

        // Map image_type to setting key and default value
        $map = [
            'hero_1' => ['key' => 'home_hero_image_1', 'default' => '/images/hero.jpg'],
            'hero_2' => ['key' => 'home_hero_image_2', 'default' => '/images/hero.jpg'],
            'hero_3' => ['key' => 'home_hero_image_3', 'default' => '/images/hero.jpg'],

            'story' => ['key' => 'home_story_image', 'default' => '/images/story.jpg'],

            'editorial_1' => ['key' => 'home_editorial_image_1', 'default' => '/images/editorial1.jpg'],
            'editorial_2' => ['key' => 'home_editorial_image_2', 'default' => '/images/editorial2.jpg'],
            'editorial_3' => ['key' => 'home_editorial_image_3', 'default' => '/images/editorial3.jpg'],
        ];

        $settingKey   = $map[$imageType]['key']    ?? 'home_hero_image';
        $defaultValue = $map[$imageType]['default'] ?? '/images/hero.jpg';

        Setting::set($settingKey, $defaultValue);

        return back()->with('success', 'Image reset to default');
    }

    /**
     * Update home page visibility settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'show_story_section' => 'nullable|boolean',
            'show_featured_products' => 'nullable|boolean',
            'show_editorial_grid' => 'nullable|boolean',
            'featured_products_count' => 'required|integer|min:1|max:20',
        ]);

        Setting::set('home_show_story_section', $request->has('show_story_section'));
        Setting::set('home_show_featured_products', $request->has('show_featured_products'));
        Setting::set('home_show_editorial_grid', $request->has('show_editorial_grid'));
        Setting::set('home_featured_products_count', $validated['featured_products_count']);

        return back()->with('success', 'Home page settings updated successfully');
    }
}
