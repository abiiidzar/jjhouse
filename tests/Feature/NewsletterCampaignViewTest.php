<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscription;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class NewsletterCampaignViewTest extends TestCase
{
    use DatabaseMigrations;

    public function test_admin_can_open_newsletter_campaign_create_page_and_submit(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::query()->create([
            'name' => 'admin',
            'display_name' => 'Admin',
        ]));

        NewsletterSubscription::query()->create([
            'email' => 'sub@example.com',
            'name' => 'Subscriber',
            'is_active' => true,
            'token' => 'tok-123',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.newsletter.campaign'))
            ->assertOk()
            ->assertSee('Create Newsletter Campaign')
            ->assertSee('name="send_to"', false)
            ->assertSee('name="subject"', false)
            ->assertSee('name="body"', false);

        $this->actingAs($admin)
            ->post(route('admin.newsletter.send'), [
                'send_to' => 'active',
                'subject' => 'Promo Akhir Tahun',
                'body' => '<p>Diskon spesial minggu ini.</p>',
            ])
            ->assertRedirect(route('admin.newsletter.campaign'));
    }

    public function test_admin_can_toggle_and_filter_newsletter_subscriber_status(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::query()->create([
            'name' => 'admin',
            'display_name' => 'Admin',
        ]));

        $activeSub = NewsletterSubscription::query()->create([
            'email' => 'active@example.com',
            'is_active' => true,
        ]);

        $inactiveSub = NewsletterSubscription::query()->create([
            'email' => 'paused@example.com',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.newsletter.subscribers.index'))
            ->assertOk()
            ->assertSee('active@example.com')
            ->assertSee('paused@example.com')
            ->assertSee(route('admin.newsletter.subscribers.toggle-status', $activeSub->id), false);

        $this->actingAs($admin)
            ->get(route('admin.newsletter.subscribers.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee('paused@example.com')
            ->assertDontSee('active@example.com');

        $this->actingAs($admin)
            ->patch(route('admin.newsletter.subscribers.toggle-status', $activeSub->id))
            ->assertRedirect();

        $this->assertFalse($activeSub->fresh()->is_active);

        $this->actingAs($admin)
            ->patch(route('admin.newsletter.subscribers.toggle-status', $inactiveSub->id))
            ->assertRedirect();

        $this->assertTrue($inactiveSub->fresh()->is_active);
    }
}

