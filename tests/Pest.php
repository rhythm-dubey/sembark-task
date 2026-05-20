<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

use App\Models\Role;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

function createUserWithRole(string $roleName, ?int $companyId = null): User
{
    return User::query()->create([
        'name' => ucfirst(str_replace('_', ' ', $roleName)),
        'email' => fake()->unique()->safeEmail(),
        'password' => Hash::make('password'),
        'role_id' => Role::query()->where('name', $roleName)->value('id'),
        'company_id' => $companyId,
        'email_verified_at' => now(),
    ]);
}

function createShortUrlFor(User $user, string $originalUrl): ShortUrl
{
    return ShortUrl::query()->create([
        'company_id' => $user->company_id,
        'user_id' => $user->id,
        'code' => Str::lower(Str::random(8)),
        'original_url' => $originalUrl,
        'hits' => 0,
    ]);
}
