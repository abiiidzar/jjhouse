<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_notification_history(): void
    {
        $user = User::factory()->create();
        $notification = $this->createNotificationFor($user);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertViewHas('notifications', fn ($notifications) => $notifications->contains($notification));
    }

    public function test_user_can_mark_their_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notification = $this->createNotificationFor($user, route('dashboard'));

        $this->from(route('notifications.index'))
            ->actingAs($user)
            ->patch(route('notifications.read', $notification->id))
            ->assertRedirect(route('notifications.index'));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $notification = $this->createNotificationFor($otherUser);

        $this->actingAs($user)
            ->patch(route('notifications.read', $notification->id))
            ->assertNotFound();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_of_their_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $first = $this->createNotificationFor($user);
        $second = $this->createNotificationFor($user);
        $other = $this->createNotificationFor($otherUser);

        $this->actingAs($user)
            ->patch(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertNotNull($first->fresh()->read_at);
        $this->assertNotNull($second->fresh()->read_at);
        $this->assertNull($other->fresh()->read_at);
    }

    public function test_user_can_fetch_only_their_unread_notification_count(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->createNotificationFor($user);
        $this->createNotificationFor($user);
        $this->createNotificationFor($otherUser);

        $this->actingAs($user)
            ->getJson(route('notifications.unread-count'))
            ->assertOk()
            ->assertJsonPath('count', 2);
    }

    private function createNotificationFor(User $user, ?string $actionUrl = null): DatabaseNotification
    {
        return DatabaseNotification::query()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [
                'title' => 'Pesanan diperbarui',
                'message' => 'Status pesanan Anda berubah.',
                'action_url' => $actionUrl ?? route('notifications.index'),
            ],
        ]);
    }
}
