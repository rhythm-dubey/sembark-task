<?php

namespace App\Services;

use App\Models\ShortUrl;
use Illuminate\Support\Str;

class ShortUrlCodeGenerator
{
    public function generate(): string
    {
        do {
            $code = Str::lower(Str::random(8));
        } while (ShortUrl::query()->where('code', $code)->exists());

        return $code;
    }
}
