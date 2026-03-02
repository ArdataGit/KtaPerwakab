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

    {{-- ==================== DESKTOP VIEW ==================== --}}
    <x-slot:desktop>
        <div class="min-h-screen bg-gray-50 flex">
            {{-- Left: Branding --}}
            <div class="hidden lg:flex w-1/2 bg-green-700 items-center justify-center relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-green-600 rounded-full mix-blend-multiply opacity-50 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-emerald-500 rounded-full mix-blend-multiply opacity-50 blur-3xl"></div>
                <div class="relative z-10 p-12 text-white flex flex-col justify-center h-full max-w-lg">
                    <img src="/images/assets/logo.png" class="w-32 mb-8 drop-shadow-lg bg-white/10 p-4 rounded-3xl backdrop-blur-md border border-white/20" onerror="this.src='/images/assets/iuran.png'">
                    <h1 class="text-5xl font-extrabold mb-6 leading-tight tracking-tight">Atur Ulang<br>Password Anda</h1>
                    <p class="text-lg text-green-100 mb-10 leading-relaxed font-medium">Jangan khawatir, kami akan membantu Anda mengatur ulang password akun KTA Digital Perwakab dengan mudah dan aman.</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <p class="text-sm text-green-200">Data Anda dilindungi dan aman bersama kami.</p>
                    </div>
                </div>
            </div>

            {{-- Right: Form --}}
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-24 bg-white relative">
                <div class="w-full max-w-md mx-auto relative z-10">

                    {{-- Snackbar --}}
                    @if($snackbar['message'])
                        <div class="mb-6 {{ $snackbar['type'] === 'error' ? 'bg-red-50 border-l-4 border-red-500' : 'bg-green-50 border-l-4 border-green-500' }} p-4 rounded-r-lg flex items-center shadow-sm">
                            <p class="text-sm font-medium {{ $snackbar['type'] === 'error' ? 'text-red-800' : 'text-green-800' }}">{{ $snackbar['message'] }}</p>
                        </div>
                    @endif

                    {{-- Logo mobile --}}
                    <div class="lg:hidden flex justify-center mb-8">
                        <img src="/images/assets/logo.png" class="w-24">
                    </div>

                    {{-- STEP 1: Pilih Metode --}}
                    @if(!$method)
                        <div class="mb-10 text-center lg:text-left">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">Lupa Password? 🔑</h2>
                            <p class="text-gray-500">Pilih metode untuk mengatur ulang password akun Anda.</p>
                        </div>

                        <div class="space-y-4">
                            <button wire:click="$set('method','email')" class="w-full flex items-center gap-4 p-5 rounded-xl border-2 border-gray-200 bg-white hover:border-green-500 hover:shadow-md transition cursor-pointer group">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-green-200 transition">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="text-left">
                                    <h3 class="font-semibold text-gray-800">Reset via Email</h3>
                                    <p class="text-sm text-gray-500">Link reset akan dikirim ke email terdaftar</p>
                                </div>
                            </button>

                            <button wire:click="$set('method','whatsapp')" class="w-full flex items-center gap-4 p-5 rounded-xl border-2 border-gray-200 bg-white hover:border-green-500 hover:shadow-md transition cursor-pointer group">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-green-200 transition">
                                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </div>
                                <div class="text-left">
                                    <h3 class="font-semibold text-gray-800">Hubungi Admin via WhatsApp</h3>
                                    <p class="text-sm text-gray-500">Chat langsung dengan admin kami</p>
                                </div>
                            </button>
                        </div>

                        <div class="mt-8 pt-8 border-t border-gray-100 text-center">
                            <p class="text-sm text-gray-500 font-medium">Ingat password Anda? <a href="/login" class="font-bold text-green-600 hover:text-green-800 ml-1">Kembali ke Login &rarr;</a></p>
                        </div>

                    {{-- STEP 2: Email Form --}}
                    @elseif($method === 'email')
                        @if($success)
                            <div class="text-center py-8">
                                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-800 mb-3">Email Terkirim! ✉️</h2>
                                <p class="text-gray-600 mb-8">Kami telah mengirim link reset password ke email <strong class="text-green-700">{{ $email }}</strong>. Silakan cek inbox Anda.</p>
                                <a href="/login" class="inline-block w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition shadow-lg shadow-green-200 text-center">Kembali ke Login</a>
                            </div>
                        @else
                            <div class="mb-10 text-center lg:text-left">
                                <button wire:click="$set('method','')" class="text-sm text-gray-400 hover:text-green-600 transition mb-4 inline-flex items-center gap-1">&larr; Kembali pilih metode</button>
                                <h2 class="text-3xl font-bold text-gray-900 mb-2">Reset via Email ✉️</h2>
                                <p class="text-gray-500">Masukkan alamat email yang terdaftar di akun Anda.</p>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                                    <input wire:model.defer="email" type="email" placeholder="contoh@email.com"
                                        class="w-full px-4 py-3.5 rounded-xl border {{ isset($errors['email']) ? 'border-red-300 ring-2 ring-red-100' : 'border-gray-200 focus:border-green-500 focus:ring-4 focus:ring-green-100' }} transition outline-none text-sm font-medium">
                                    @if(isset($errors['email']))<p class="text-red-500 text-xs mt-2">{{ $errors['email'] }}</p>@endif
                                </div>

                                <button wire:click="submit" {{ $loading ? 'disabled' : '' }}
                                    class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition shadow-lg shadow-green-200 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                    @if($loading)
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                        Mengirim...
                                    @else
                                        Kirim Link Reset Password
                                    @endif
                                </button>
                            </div>
                        @endif

                    {{-- STEP 3: WhatsApp --}}
                    @elseif($method === 'whatsapp')
                        <div class="mb-10 text-center lg:text-left">
                            <button wire:click="$set('method','')" class="text-sm text-gray-400 hover:text-green-600 transition mb-4 inline-flex items-center gap-1">&larr; Kembali pilih metode</button>
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">Hubungi Admin 💬</h2>
                            <p class="text-gray-500">Klik tombol di bawah untuk menghubungi admin melalui WhatsApp dan meminta reset password.</p>
                        </div>

                        <a href="https://wa.me/6281234567890?text=Halo%20Admin%2C%20saya%20ingin%20reset%20password" target="_blank"
                            class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition shadow-lg shadow-green-200 text-center block">
                            Chat Admin via WhatsApp
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </x-slot:desktop>

</x-layouts.mobile>