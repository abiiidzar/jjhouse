<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Setting;

class LoginController extends Controller
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
    return view('auth.login', compact('homeImages'));
    }
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->load('roles');

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('karyawan')) {
                return redirect()->route('karyawan.dashboard');
            }

            if ($user->hasRole('kasir')) {
                return redirect()->route('kasir.dashboard');
            }

            $returnProduct = $request->input('return_product');
            if (is_string($returnProduct)) {
                $product = Product::query()
                    ->where('slug', $returnProduct)
                    ->where('is_active', true)
                    ->first();

                if ($product) {
                    return redirect()->route('product.show', $product->slug);
                }
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

}
