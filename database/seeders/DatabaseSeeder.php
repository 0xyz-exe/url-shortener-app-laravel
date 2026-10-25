<?php

namespace Database\Seeders;

use App\Models\ShortUrl;
use App\Models\ShortUrlClick;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin & Test Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $testUser = User::create([
            'name' => 'Alex Rivera',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // 2. Short Links
        $link1 = ShortUrl::create([
            'user_id' => $testUser->id,
            'title' => 'Laravel Documentation',
            'original_url' => 'https://laravel.com/docs',
            'slug' => 'docs',
            'clicks_count' => 15,
            'is_active' => true,
            'expires_at' => null,
        ]);

        $link2 = ShortUrl::create([
            'user_id' => $testUser->id,
            'title' => 'GitHub Profile',
            'original_url' => 'https://github.com/0xyz-exe',
            'slug' => 'ghub',
            'clicks_count' => 8,
            'is_active' => true,
            'expires_at' => null,
        ]);

        $link3 = ShortUrl::create([
            'user_id' => $testUser->id,
            'title' => 'Spring 2026 Promo Campaign',
            'original_url' => 'https://example.com/spring-sale',
            'slug' => 'spring26',
            'clicks_count' => 5,
            'is_active' => true,
            'expires_at' => now()->addMonth(),
        ]);

        $link4 = ShortUrl::create([
            'user_id' => $testUser->id,
            'title' => 'Paused Marketing Test',
            'original_url' => 'https://example.com/beta-test',
            'slug' => 'betatest',
            'clicks_count' => 2,
            'is_active' => false,
            'expires_at' => null,
        ]);

        $link5 = ShortUrl::create([
            'user_id' => $admin->id,
            'title' => 'Expired Flash Sale',
            'original_url' => 'https://example.com/flash-sale',
            'slug' => 'flash24',
            'clicks_count' => 12,
            'is_active' => true,
            'expires_at' => now()->subDay(),
        ]);

        // 3. Simulated Clicks with devices, browsers, and referrers
        $userAgents = [
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 Edg/121.0.0.0',
            'Mozilla/5.0 (X11; Linux x86_64; rv:122.0) Gecko/20100101 Firefox/122.0',
        ];

        $referrers = [
            'https://twitter.com/',
            'https://linkedin.com/',
            'https://t.co/abc',
            'https://google.com/search?q=swiftlink',
            null, // Direct
        ];

        $ips = ['192.168.1.101', '172.56.21.8', '198.51.100.42', '203.0.113.19'];

        foreach ([$link1, $link2, $link3] as $link) {
            for ($i = 0; $i < $link->clicks_count; $i++) {
                ShortUrlClick::create([
                    'short_url_id' => $link->id,
                    'ip_address' => $ips[$i % count($ips)],
                    'user_agent' => $userAgents[$i % count($userAgents)],
                    'referer' => $referrers[$i % count($referrers)],
                    'created_at' => now()->subHours(rand(1, 72)),
                ]);
            }
        }
    }
}
