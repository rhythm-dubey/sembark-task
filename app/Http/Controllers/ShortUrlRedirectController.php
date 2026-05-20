<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShortUrlRedirectController extends Controller
{
    public function __invoke(Request $request, string $code): RedirectResponse
    {
        $this->authorize('resolve', ShortUrl::class);

        $shortUrl = ShortUrl::query()->where('code', $code)->firstOrFail();

        $shortUrl->increment('hits');

        return redirect()->away($shortUrl->original_url);
    }
}
