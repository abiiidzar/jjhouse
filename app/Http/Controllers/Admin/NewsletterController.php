<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NewsletterSubscription;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscriptions,email',
        ]);

        try {
            DB::table('newsletter_subscriptions')->insert([
                'email' => $request->email,
                'created_at' => now(),
            ]);

            return back()->with('success', 'Thank you for subscribing to our newsletter!');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred. Please try again.');
        }
    }
}
