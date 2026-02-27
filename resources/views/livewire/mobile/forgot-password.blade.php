<?php

use App\Services\AuthApiService;
use function Livewire\Volt\state;

state([
    'email' => '',
    'method' => '', // email | whatsapp
    'snackbar' => ['type' => '', 'message' => ''],
    'errors' => [],
    'loading' => false,
    'success' => false,
]);

$submit = function () {

    $this->errors = [];
    $this->success = false;

    if (!$this->email) {
        $this->errors['email'] = 'Email wajib diisi';
        $this->snackbar = ['type' => 'error', 'message' => 'Email wajib diisi'];
        return;
    }

    if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
        $this->errors['email'] = 'Format email tidak valid';
        $this->snackbar = ['type' => 'error', 'message' => 'Format email tidak valid'];
        return;
    }

    $emailParts = explode('@', $this->email);
    if (count($emailParts) !== 2 || strpos($emailParts[1], '.') === false) {
        $this->errors['email'] = 'Format email tidak valid';
        $this->snackbar = ['type' => 'error', 'message' => 'Format email tidak valid'];
        return;
    }

    $this->loading = true;

    try {

        \Log::info('Forgot Password Request', ['email' => $this->email]);

        $response = AuthApiService::forgotPassword($this->email);

        \Log::info('Forgot Password Response', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        if ($response->successful()) {

            $this->success = true;

            $this->snackbar = [
                'type' => 'success',
                'message' => $response->json('message') ?? 'Link reset password telah dikirim ke email Anda'
            ];

        } else {

            $data = $response->json();
            $message = $data['message'] ?? 'Gagal mengirim email reset password';

            if ($response->status() === 422 && isset($data['errors']['email'])) {
                $this->errors['email'] = is_array($data['errors']['email'])
                    ? $data['errors']['email'][0]
                    : $data['errors']['email'];

                $message = $this->errors['email'];
            }

            if ($response->status() === 429) {
                $message = $data['message']
                    ?? 'Anda sudah meminta reset password. Silakan cek email Anda atau tunggu beberapa menit.';
            }

            $this->snackbar = ['type' => 'error', 'message' => $message];
        }

    } catch (\Illuminate\Http\Client\ConnectionException $e) {

        $this->snackbar = [
            'type' => 'error',
            'message' => 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.'
        ];

    } catch (\Exception $e) {

        \Log::error('Forgot Password Error', ['error' => $e->getMessage()]);

        $this->snackbar = [
            'type' => 'error',
            'message' => 'Terjadi kesalahan. Silakan coba lagi.'
        ];

    } finally {
        $this->loading = false;
    }
};
?>

<x-layouts.mobile title="Lupa Password">

    <!-- SNACKBAR -->
    <div x-data="{
        snackbar: @entangle('snackbar'),
        show: false,
        _timeout: null,
        icons: { error: '⚠', success: '✔' },
        styles: {
            error: 'bg-red-500 text-white',
            success: 'bg-green-600 text-white'
        },
        showAndAutoHide() {
            if (!this.snackbar || !this.snackbar.message) return;
            this.show = true;
            if (this._timeout) clearTimeout(this._timeout);
            this._timeout = setTimeout(() => {
                this.show = false;
                this._timeout = null;
                try {
                    @this.set('snackbar', { type: '', message: '' });
                } catch (e) {}
            }, 3000);
        }
    }"
    x-init="
        $watch('snackbar', value => {
            if (value && value.message) showAndAutoHide();
        }, { immediate: true });
    "
    x-show="show"
    x-cloak
    x-transition.opacity.duration.300ms
    class="fixed top-0 left-1/2 -translate-x-1/2 w-[390px] z-[9999] flex items-center gap-2 px-4 py-3 text-sm font-medium shadow-lg rounded-b-lg"
    :class="styles[snackbar?.type ?? 'success']">

        <span class="text-lg" x-text="icons[snackbar?.type ?? 'success']"></span>
        <span x-text="snackbar?.message ?? ''"></span>

    </div>

    <div class="relative w-full min-h-screen flex flex-col">

        <img src="/images/assets/bg-pattern.png"
            class="absolute inset-0 w-full h-full object-cover pointer-events-none" />

        <div class="relative z-10 px-6 pt-10">

            <a href="/login" class="text-white text-xl">&larr;</a>

            <div class="flex justify-center mt-4 mb-3">
                <img src="/images/assets/logo.png" class="w-20">
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-6 shadow-lg">

                {{-- STEP 1 : PILIH METODE --}}
                @if(!$method)

                    <h1 class="text-center font-bold text-xl mb-4">Lupa Password</h1>
                    <p class="text-center text-sm text-gray-600 mb-6">
                        Pilih metode untuk reset password
                    </p>

                    <div class="flex flex-col gap-3">

                        <button
                            wire:click="$set('method','email')"
                            class="w-full bg-green-600 text-white py-3 rounded-lg font-medium">
                            Reset via Email
                        </button>

                        <button
                            wire:click="$set('method','whatsapp')"
                            class="w-full bg-green-600 text-white py-3 rounded-lg font-medium">
                            Hubungi Admin via WhatsApp
                        </button>

                    </div>

                    <p class="text-center text-sm mt-4 text-gray-600">
                        <a href="/login" class="text-green-700 font-semibold">
                            Kembali ke Login
                        </a>
                    </p>

                {{-- STEP 2 : EMAIL --}}
                @elseif($method === 'email')

                    @if($success)

                        <div class="text-center py-4">

                            <div class="mb-4">
                                <svg class="w-16 h-16 mx-auto text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>

                            <h2 class="text-xl font-bold text-gray-800 mb-2">
                                Email Terkirim
                            </h2>

                            <p class="text-sm text-gray-600 mb-6">
                                Kami telah mengirim link reset password ke email
                                <strong>{{ $email }}</strong>.
                            </p>

                            <a href="/login"
                               class="block w-full bg-green-600 text-white py-3 rounded-lg font-medium text-center">
                                Kembali ke Login
                            </a>

                        </div>

                    @else

                        <button
                            wire:click="$set('method','')"
                            class="text-sm text-gray-600 mb-3">
                            &larr; Kembali
                        </button>

                        <h1 class="text-center font-bold text-xl mb-2">
                            Reset via Email
                        </h1>

                        <x-mobile.input
                            wire:model.defer="email"
                            placeholder="Email"
                            type="email"
                            :disabled="$loading"
                            :invalid="isset($errors['email'])" />

                        @if(isset($errors['email']))
                            <p class="text-red-500 text-xs mt-1 ml-1">
                                {{ $errors['email'] }}
                            </p>
                        @endif

                        <x-mobile.button
                            class="mt-4"
                            wire:click="submit"
                            :disabled="$loading">

                            @if($loading)
                                Mengirim...
                            @else
                                Kirim Link Reset Password
                            @endif

                        </x-mobile.button>

                    @endif

                {{-- STEP 3 : WHATSAPP --}}
                @elseif($method === 'whatsapp')

                    <button
                        wire:click="$set('method','')"
                        class="text-sm bg-blue text-gray-600 mb-3">
                        &larr; Kembali
                    </button>

                    <h1 class="text-center font-bold text-xl mb-2">
                        Hubungi Admin
                    </h1>

                    <p class="text-center text-sm text-gray-600 mb-6">
                        Klik tombol di bawah untuk menghubungi admin via WhatsApp
                    </p>

                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%2C%20saya%20ingin%20reset%20password"
                       target="_blank"
                       class="block w-full bg-green-500 text-white py-3 rounded-lg font-medium text-center">
                        Chat Admin via WhatsApp
                    </a>

                @endif

            </div>

        </div>

    </div>

</x-layouts.mobile>