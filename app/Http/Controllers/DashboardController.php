<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Models\ShortUrlClick;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $query = $user->isAdmin() ? ShortUrl::query() : $user->shortUrls();

        $totalLinks = (clone $query)->count();
        $totalClicks = (clone $query)->sum('clicks_count');
        $activeLinks = (clone $query)->where('is_active', true)->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->count();

        $topLinks = (clone $query)->orderByDesc('clicks_count')->take(5)->get();
        $recentLinks = (clone $query)->latest()->take(5)->get();

        $linkIds = (clone $query)->pluck('id');
        $recentClicks = ShortUrlClick::whereIn('short_url_id', $linkIds)
            ->with('shortUrl')
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'totalLinks',
            'totalClicks',
            'activeLinks',
            'topLinks',
            'recentLinks',
            'recentClicks'
        ));
    }
}
