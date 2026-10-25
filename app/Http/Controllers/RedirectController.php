<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RedirectController extends Controller
{
    public function __invoke(string $slug, Request $request): RedirectResponse|View
    {
        $shortUrl = ShortUrl::where('slug', $slug)->first();

        if (!$shortUrl) {
            abort(404, 'Short URL not found.');
        }

        if (!$shortUrl->is_active) {
            return view('short-urls.inactive', [
                'shortUrl' => $shortUrl,
                'reason' => 'This link has been deactivated by its owner.',
            ]);
        }

        if ($shortUrl->isExpired()) {
            return view('short-urls.inactive', [
                'shortUrl' => $shortUrl,
                'reason' => 'This link expired on ' . $shortUrl->expires_at->format('M d, Y H:i T'),
            ]);
        }

        // Record click analytics
        $shortUrl->recordClick($request);

        return redirect()->away($shortUrl->original_url);
    }
}
