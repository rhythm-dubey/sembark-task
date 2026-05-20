<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortUrlRequest;
use App\Models\ShortUrl;
use App\Services\ShortUrlCodeGenerator;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ShortUrlController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', ShortUrl::class);

        $shortUrls = ShortUrl::query()
            ->visibleTo(auth()->user())
            ->with(['company', 'user'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('short-urls/Index', [
            'shortUrls' => $shortUrls,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', ShortUrl::class);

        return Inertia::render('short-urls/Create');
    }

    public function store(StoreShortUrlRequest $request, ShortUrlCodeGenerator $codeGenerator): RedirectResponse
    {
        $user = $request->user();

        ShortUrl::query()->create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'code' => $codeGenerator->generate(),
            'original_url' => $request->validated('original_url'),
        ]);

        return redirect()
            ->route('short-urls.index')
            ->with('status', 'Short URL created.');
    }
}
