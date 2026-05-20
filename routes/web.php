<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\ShortUrlRedirectController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');

    Route::resource('short-urls', ShortUrlController::class)->only(['index', 'create', 'store']);

    Route::get('invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('invitations', [InvitationController::class, 'store'])->name('invitations.store');

    Route::get('s/{code}', ShortUrlRedirectController::class)->name('short-urls.resolve');
});

require __DIR__.'/settings.php';
