<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletter subscribers.
     */
    public function index()
    {
        $subscribers = NewsletterSubscription::latest()->paginate(20);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    /**
     * Show the form for creating a new subscriber.
     */
    public function create()
    {
        return view('admin.newsletter.subscribers.create');
    }

    /**
     * Store a newly created subscriber in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletter_subscriptions,email',
            'name' => 'nullable|string|max:255',
        ]);

        $validated['token'] = Str::random(60);
        $validated['is_active'] = true;

        NewsletterSubscription::create($validated);

        return redirect()->route('admin.newsletter.subscribers.index')
            ->with('success', 'Subscriber added successfully');
    }

    /**
     * Subscribe to newsletter (public route)
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
        ]);

        // Check if already subscribed
        $exists = NewsletterSubscription::where('email', $validated['email'])->first();

        if ($exists && $exists->is_active) {
            return back()->with('info', 'Email sudah terdaftar di newsletter kami');
        }

        // If previously unsubscribed, reactivate
        if ($exists && !$exists->is_active) {
            $exists->update(['is_active' => true, 'token' => Str::random(60)]);
            return back()->with('success', 'Subscription reactivated! Check your email for confirmation.');
        }

        // Create new subscription
        $validated['token'] = Str::random(60);
        $validated['is_active'] = false; // Require confirmation

        $subscription = NewsletterSubscription::create($validated);

        // Send confirmation email
        try {
            Mail::send('emails.newsletter.confirmation', [
                'email' => $subscription->email,
                'token' => $subscription->token,
                'name' => $subscription->name,
            ], function ($message) use ($subscription) {
                $message->to($subscription->email)
                    ->subject('Confirm Your Newsletter Subscription');
            });
        } catch (\Exception $e) {
            \Log::error('Newsletter subscription email error: ' . $e->getMessage());
        }

        return back()->with('success', 'Terima kasih! Silakan cek email Anda untuk mengkonfirmasi subscription');
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe($token)
    {
        $subscription = NewsletterSubscription::where('token', $token)->first();

        if (!$subscription) {
            return redirect('/')->with('error', 'Invalid unsubscribe link');
        }

        $subscription->update(['is_active' => false]);

        return redirect('/')->with('success', 'Anda telah berhasil unsubscribe dari newsletter kami');
    }

    /**
     * Destroy a subscriber
     */
    public function destroy(NewsletterSubscription $subscriber)
    {
        $subscriber->delete();

        return redirect()->route('admin.newsletter.subscribers.index')
            ->with('success', 'Subscriber deleted successfully');
    }

    /**
     * Show campaign creation form
     */
    public function campaign()
    {
        return view('admin.newsletter.campaign.create');
    }

    /**
     * Send newsletter campaign
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'send_to' => 'required|in:all,active',
        ]);

        $subscribers = $validated['send_to'] === 'all'
            ? NewsletterSubscription::all()
            : NewsletterSubscription::where('is_active', true)->get();

        if ($subscribers->isEmpty()) {
            return back()->with('error', 'No subscribers to send to');
        }

        // Queue the email sending
        foreach ($subscribers as $subscriber) {
            try {
                Mail::send('emails.newsletter.campaign', [
                    'subject' => $validated['subject'],
                    'body' => $validated['body'],
                    'unsubscribe_token' => $subscriber->token,
                ], function ($message) use ($subscriber, $validated) {
                    $message->to($subscriber->email)
                        ->subject($validated['subject']);
                });
            } catch (\Exception $e) {
                \Log::error('Newsletter campaign send error: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.newsletter.subscribers.campaign')
            ->with('success', 'Campaign sent to ' . $subscribers->count() . ' subscribers');
    }

    /**
     * Export subscribers
     */
    public function export()
    {
        $subscribers = NewsletterSubscription::where('is_active', true)->get();

        $csv = "Email,Name,Subscribed Date\n";
        foreach ($subscribers as $subscriber) {
            $csv .= "\"{$subscriber->email}\",\"{$subscriber->name}\",\"{$subscriber->created_at->format('Y-m-d')}\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="newsletter_subscribers.csv"');
    }

    /**
     * Show campaign history
     */
    public function history()
    {
        // This would show previous campaigns if you have a campaigns table
        // For now, it's a placeholder
        return view('admin.newsletter.history');
    }
    public function show(NewsletterSubscription $subscriber)
    {
        return view('admin.newsletter.subscribers.show', compact('subscriber'));
    }
}
