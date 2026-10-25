<?php

namespace Tests\Feature;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlShortenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_short_link_with_auto_slug(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('short-urls.store'), [
            'original_url' => 'https://example.com/very/long/destination',
            'title' => 'Sample Long URL',
        ]);

        $response->assertRedirect(route('short-urls.index'));

        $this->assertDatabaseHas('short_urls', [
            'user_id' => $user->id,
            'title' => 'Sample Long URL',
            'original_url' => 'https://example.com/very/long/destination',
            'is_active' => true,
        ]);
    }

    public function test_user_can_create_custom_slug(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('short-urls.store'), [
            'original_url' => 'https://example.com/custom-path',
            'slug' => 'my-cool-link',
        ])->assertRedirect(route('short-urls.index'));

        $this->assertDatabaseHas('short_urls', [
            'slug' => 'my-cool-link',
        ]);
    }

    public function test_redirect_works_and_tracks_click(): void
    {
        $user = User::factory()->create();

        $shortUrl = ShortUrl::create([
            'user_id' => $user->id,
            'title' => 'Test Link',
            'original_url' => 'https://example.com/destination',
            'slug' => 'testlink',
            'clicks_count' => 0,
            'is_active' => true,
        ]);

        $response = $this->get('/testlink', [
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0)',
            'Referer' => 'https://twitter.com/',
        ]);

        $response->assertRedirect('https://example.com/destination');

        $shortUrl->refresh();
        $this->assertEquals(1, $shortUrl->clicks_count);

        $this->assertDatabaseHas('short_url_clicks', [
            'short_url_id' => $shortUrl->id,
            'referer' => 'https://twitter.com/',
        ]);
    }

    public function test_inactive_or_expired_link_shows_notice(): void
    {
        $user = User::factory()->create();

        $inactive = ShortUrl::create([
            'user_id' => $user->id,
            'original_url' => 'https://example.com/blocked',
            'slug' => 'pausedlink',
            'is_active' => false,
        ]);

        $response = $this->get('/pausedlink');
        $response->assertStatus(200);
        $response->assertSee('Link Unavailable');

        $expired = ShortUrl::create([
            'user_id' => $user->id,
            'original_url' => 'https://example.com/expired',
            'slug' => 'expiredlink',
            'is_active' => true,
            'expires_at' => now()->subHour(),
        ]);

        $respExpired = $this->get('/expiredlink');
        $respExpired->assertStatus(200);
        $respExpired->assertSee('expired');
    }

    public function test_user_can_view_link_analytics(): void
    {
        $user = User::factory()->create();

        $shortUrl = ShortUrl::create([
            'user_id' => $user->id,
            'title' => 'Analytics Target',
            'original_url' => 'https://example.com/target',
            'slug' => 'targetlink',
            'clicks_count' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('short-urls.analytics', $shortUrl));
        $response->assertStatus(200);
        $response->assertSee('Analytics Target');
        $response->assertSee('Total Clicks');
    }

    public function test_user_can_update_and_delete_link(): void
    {
        $user = User::factory()->create();

        $shortUrl = ShortUrl::create([
            'user_id' => $user->id,
            'title' => 'Initial Title',
            'original_url' => 'https://example.com/initial',
            'slug' => 'initlink',
            'is_active' => true,
        ]);

        $this->actingAs($user)->put(route('short-urls.update', $shortUrl), [
            'title' => 'Updated Title',
            'original_url' => 'https://example.com/new-dest',
            'slug' => 'updatedlink',
            'is_active' => 1,
        ])->assertRedirect(route('short-urls.index'));

        $this->assertDatabaseHas('short_urls', [
            'id' => $shortUrl->id,
            'title' => 'Updated Title',
            'slug' => 'updatedlink',
        ]);

        $this->actingAs($user)->delete(route('short-urls.destroy', $shortUrl))
            ->assertRedirect(route('short-urls.index'));

        $this->assertDatabaseMissing('short_urls', ['id' => $shortUrl->id]);
    }
}
