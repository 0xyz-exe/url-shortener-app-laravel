<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShortUrlController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = $user->isAdmin() ? ShortUrl::with('user') : $user->shortUrls();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('original_url', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true)->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                });
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->input('status') === 'expired') {
                $query->whereNotNull('expires_at')->where('expires_at', '<=', now());
            }
        }

        $shortUrls = $query->latest()->paginate(10)->withQueryString();

        return view('short-urls.index', compact('shortUrls'));
    }

    public function create(): View
    {
        return view('short-urls.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'alpha_dash', 'min:3', 'max:50', 'unique:short_urls,slug'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $slug = $validated['slug'] ?? null;
        if (empty($slug)) {
            do {
                $slug = Str::lower(Str::random(6));
            } while (ShortUrl::where('slug', $slug)->exists());
        }

        $shortUrl = ShortUrl::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'] ?? null,
            'original_url' => $validated['original_url'],
            'slug' => $slug,
            'is_active' => true,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()->route('short-urls.index')->with('status', 'Short URL created successfully: ' . $shortUrl->short_url);
    }

    public function edit(ShortUrl $shortUrl): View
    {
        $this->authorizeAccess($shortUrl);

        return view('short-urls.edit', compact('shortUrl'));
    }

    public function update(Request $request, ShortUrl $shortUrl): RedirectResponse
    {
        $this->authorizeAccess($shortUrl);

        $validated = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'alpha_dash', 'min:3', 'max:50', 'unique:short_urls,slug,' . $shortUrl->id],
            'is_active' => ['boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $shortUrl->update([
            'title' => $validated['title'] ?? null,
            'original_url' => $validated['original_url'],
            'slug' => $validated['slug'],
            'is_active' => $request->boolean('is_active'),
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()->route('short-urls.index')->with('status', 'Short URL updated.');
    }

    public function destroy(ShortUrl $shortUrl): RedirectResponse
    {
        $this->authorizeAccess($shortUrl);

        $shortUrl->delete();

        return redirect()->route('short-urls.index')->with('status', 'Short URL deleted.');
    }

    public function analytics(ShortUrl $shortUrl): View
    {
        $this->authorizeAccess($shortUrl);

        $recentClicks = $shortUrl->clicks()->latest()->take(50)->get();

        $referrers = $shortUrl->clicks()
            ->selectRaw('COALESCE(referer, "Direct / None") as referrer_source, count(*) as total')
            ->groupBy('referrer_source')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        return view('short-urls.analytics', compact('shortUrl', 'recentClicks', 'referrers'));
    }

    private function authorizeAccess(ShortUrl $shortUrl): void
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $shortUrl->user_id !== $user->id) {
            abort(403, 'Unauthorized link access.');
        }
    }
}
