<?php
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Services\AuthApiService;

/*
|--------------------------------------------------------------------------
| MOBILE PUBLIC ROUTES (TANPA LOGIN)
|--------------------------------------------------------------------------
*/
Volt::route('/', 'mobile.splash')->name('mobile.splash');
Route::middleware('mobile.guest')->group(function () {

    Volt::route('/onboarding', 'mobile.onboarding')->name('mobile.onboarding');

    Volt::route('/auth', 'mobile.auth.landing')->name('auth.landing');
    Volt::route('/login', 'mobile.login')->name('mobile.login');
    Volt::route('/register', 'mobile.register')->name('mobile.register');
    Volt::route('/forgot-password', 'mobile.forgot-password')->name('mobile.forgot-password');
    Volt::route('/reset-password', 'mobile.reset-password')->name('mobile.reset-password');

});

/*
|--------------------------------------------------------------------------
| MOBILE PROTECTED ROUTES (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('mobile.auth')->group(function () {

    // Home
    Volt::route('/home', 'mobile.home')->name('mobile.home');

    // KTA
    Volt::route('/kta', 'mobile.kta')->name('mobile.kta');

    // IURAN
    Volt::route('/iuran', 'mobile.iuran')->name('mobile.iuran');
    Volt::route('/iuran/metode', 'mobile.iuran-metode')->name('mobile.iuran.metode');
    Volt::route('/iuran/upload', 'mobile.iuran-upload')->name('mobile.iuran.upload');
    Volt::route('/iuran/saya', 'mobile.iuran-saya')->name('mobile.iuran.saya');
    Volt::route('/iuran/{id}', 'mobile.iuran-detail')->name('mobile.iuran.detail');

    // PROFILE
    Volt::route('/profile', 'mobile.profile')->name('mobile.profile');
    Volt::route('/profile/edit', 'mobile.profile-edit')->name('mobile.profile.edit');

    // STRUKTUR ORGANISASI
    Volt::route('/struktur-organisasi', 'mobile.struktur-organisasi')->name('mobile.struktur-organisasi');

    // ARTICLE
    Volt::route('/articles', 'mobile.article')->name('mobile.articles');

    Volt::route('/artikel/{id}', 'mobile.article-detail')
        ->name('mobile.article.detail');


    // MARKETPLACE
    Volt::route('/marketplace', 'mobile.marketplace.index')
        ->name('mobile.marketplace.index');

    // Marketplace Detail Produk
    Volt::route('/marketplace/{id}', 'mobile.marketplace.show')
        ->name('mobile.marketplace.show');

    // Karya
    Volt::route('/karya', 'mobile.karya.index')
        ->name('mobile.karya.index');

    // Marketplace Detail Produk
    Volt::route('/karya/{id}', 'mobile.karya.show')
        ->name('mobile.karya.show');
    // Info Duka
    Volt::route('/info-duka', 'mobile.info-duka.index')
        ->name('mobile.info-duka.index');

    // Marketplace Detail Produk
    Volt::route('/info-duka/{id}', 'mobile.info-duka.show')
        ->name('mobile.info-duka.show');
    // Marketplace Detail Produk
    Volt::route('/poin-saya', 'mobile.poin.index')
        ->name('mobile.poin.index');

    Volt::route('/tukar-poin', 'mobile.poin.tukar')
        ->name('mobile.poin.tukar');

    Volt::route('/tukar-poin/{id}', 'mobile.poin.detail')
        ->name('mobile.poin.detail');


    Volt::route('/donation-campaign', 'mobile.donation.index')
        ->name('mobile.donation.index');
    Volt::route('/donation-campaign/{id}', 'mobile.donation.detail')
        ->name('mobile.donation.detail');
    Volt::route(
        '/donation-campaign/{id}/checkout',
        'mobile.donation.checkout'
    )->name('mobile.donation.checkout');

    Route::post('/profile/photo', function (\Illuminate\Http\Request $request) {

        $token = session('token');

        $response = \App\Services\UserApiService::updatePhoto(
            $token,
            $request->file('photo')
        );

        if ($response->successful()) {
            session(['user' => $response->json('data')]);
        }

        return back();

    })->name('mobile.profile.photo');
    // LOGOUT
    Route::post('/logout', function () {

        $token = session('token');

        if ($token) {
            try {
                AuthApiService::logout($token);
            } catch (\Throwable $e) {
                // aman diabaikan
            }
        }

        session()->forget([
            'user',
            'token',
            'membership_fee_id',
            'membership_fee_amount',
        ]);

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('mobile.login');

    })->name('mobile.logout');
});
