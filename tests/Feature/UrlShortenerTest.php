<?php

use App\Models\Company;

test('admin and member can create short urls', function (string $roleName) {
    $company = Company::query()->create(['name' => 'Test Co']);
    $user = createUserWithRole($roleName, $company->id);

    $this->actingAs($user)
        ->get(route('short-urls.create'))
        ->assertOk();

    $this->actingAs($user)
        ->post(route('short-urls.store'), [
            'original_url' => 'https://example.com/page',
        ])
        ->assertRedirect(route('short-urls.index'));
})->with([
    'admin' => 'admin',
    'member' => 'member',
]);

test('super admin cannot create short urls', function () {
    $user = createUserWithRole('super_admin');

    $this->actingAs($user)
        ->get(route('short-urls.create'))
        ->assertForbidden();

    $this->actingAs($user)
        ->post(route('short-urls.store'), [
            'original_url' => 'https://example.com/page',
        ])
        ->assertForbidden();
});

test('admin can see short urls in their own company only', function () {
    $companyA = Company::query()->create(['name' => 'Company A']);
    $companyB = Company::query()->create(['name' => 'Company B']);

    $admin = createUserWithRole('admin', $companyA->id);
    $memberInA = createUserWithRole('member', $companyA->id);
    $memberInB = createUserWithRole('member', $companyB->id);

    $urlInOwnCompany = createShortUrlFor($memberInA, 'https://company-a.test');
    $urlInOtherCompany = createShortUrlFor($memberInB, 'https://company-b.test');

    $this->actingAs($admin)
        ->get(route('short-urls.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('short-urls/Index')
            ->where('shortUrls.data', function ($data) use ($urlInOtherCompany, $urlInOwnCompany) {
                $urls = collect($data)->pluck('original_url');

                return $urls->count() === 1
                    && $urls->contains($urlInOwnCompany->original_url)
                    && ! $urls->contains($urlInOtherCompany->original_url);
            })
        );
});

test('member can only see short urls they created', function () {
    $company = Company::query()->create(['name' => 'Acme']);
    $member = createUserWithRole('member', $company->id);
    $otherMember = createUserWithRole('member', $company->id);

    createShortUrlFor($member, 'https://member-own.test');
    $otherMemberUrl = createShortUrlFor($otherMember, 'https://other-member.test');

    $this->actingAs($member)
        ->get(route('short-urls.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('short-urls/Index')
            ->where('shortUrls.data', function ($data) use ($otherMemberUrl) {
                $urls = collect($data)->pluck('original_url');

                return $urls->count() === 1
                    && $urls->contains('https://member-own.test')
                    && ! $urls->contains($otherMemberUrl->original_url);
            })
        );
});

test('super admin can see all short urls', function () {
    $company = Company::query()->create(['name' => 'Client']);
    $member = createUserWithRole('member', $company->id);
    $superAdmin = createUserWithRole('super_admin');

    createShortUrlFor($member, 'https://global-list.test');

    $this->actingAs($superAdmin)
        ->get(route('short-urls.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('short-urls/Index')
            ->where('shortUrls.data', fn ($data) => count($data) === 1)
        );
});

test('short urls are not publicly resolvable and redirect to the original url when authenticated', function () {
    $company = Company::query()->create(['name' => 'Resolve Co']);
    $user = createUserWithRole('member', $company->id);
    $shortUrl = createShortUrlFor($user, 'https://destination.example/path');

    $this->get(route('short-urls.resolve', $shortUrl->code))
        ->assertRedirect(route('login'));

    $this->actingAs($user)
        ->get(route('short-urls.resolve', $shortUrl->code))
        ->assertRedirect('https://destination.example/path');

    expect($shortUrl->fresh()->hits)->toBe(1);
});
