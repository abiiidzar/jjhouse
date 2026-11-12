<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    protected $table = 'newsletter_subscriptions'; // optional (Laravel auto detect)

    protected $fillable = [
        'email',
        'is_active',
        'unsubscribe_token'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
