# SwiftLink - URL Shortener & Click Analytics

Repository: https://github.com/0xyz-exe/url-shortener-app-laravel

A high-performance URL shortener, link management platform, and traffic analytics dashboard built with Laravel 13, Blade, Tailwind CSS, and SQLite.

## Features

- ✅ **Instant Shortening**: Auto-generates clean, collision-free 6-character random slugs or allows custom aliases.
- ✅ **Dynamic Redirection**: Lightning-fast 302/301 redirects with active and expiration validity checks.
- ✅ **Click Analytics**: Tracks visitor IP addresses, referrers, device types (Mobile/Tablet/Desktop), and browser agents.
- ✅ **Lifecycle Controls**: Pause/resume links on demand or set precise expiration dates for limited-time promotions.
- ✅ **One-Click Copy**: Instant clipboard copy button for rapid sharing across teams and campaigns.
- ✅ **Role Separation**: Standard users manage their personal short links; Administrators can inspect and moderate all links across the platform.

## Tech Stack

Laravel 13.38.0 · Breeze 2.4 · SQLite · Tailwind CSS

## Installation

```bash
git clone https://github.com/0xyz-exe/url-shortener-app-laravel.git
cd url-shortener-app-laravel
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

### Demo Accounts

All accounts use the password: `password`

- **Admin**: `admin@example.com` (Manage, inspect, and moderate all platform links)
- **User**: `test@example.com` (Alex Rivera - personal link management and analytics)

## Database Schema

- **users**: `id`, `name`, `email`, `password`, `is_admin`, `timestamps`
- **short_urls**: `id`, `user_id`, `title`, `original_url`, `slug`, `clicks_count`, `is_active`, `expires_at`, `timestamps`
- **short_url_clicks**: `id`, `short_url_id`, `ip_address`, `user_agent`, `referer`, `timestamps`

## License

MIT
