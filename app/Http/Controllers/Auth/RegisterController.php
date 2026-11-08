<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
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

        // PERBAIKAN: Gunakan view register, bukan login
        return view('auth.register', compact('homeImages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful!');
    }
}
