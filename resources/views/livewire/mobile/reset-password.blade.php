<?php

use App\Services\AuthApiService;
use function Livewire\Volt\state;
use function Livewire\Volt\mount;

state([
    'token' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
    'snackbar' => ['type' => '', 'message' => ''],
    'errors' => [],
    'loading' => false,
    'validating' => true,
    'tokenValid' => false,
    'tokenError' => '',
    'success' => false,
]);

mount(function () {
    // Get token from URL - Validasi Token Required
    $this->token = request()->query('token', '');

    if (!$this->token) {
        $this->tokenError = 'Token tidak ditemukan di URL';
        $this->snackbar = ['type' => 'error', 'message' => 'Token tidak ditemukan'];
        $this->validating = false;
        return;
    }

    // Validate token - Validasi Token Valid & Belum Digunakan & Belum Expired
    try {
        $response = AuthApiService::validateResetToken($this->token);

        if ($response->successful()) {
            $this->tokenValid = true;
            $this->email = $response->json('data.email', '');
        } else {
            $data = $response->json();
            $message = $data['message'] ?? 'Token tidak valid';
            
            // Specific error messages
            if (str_contains($message, 'sudah digunakan')) {
                $this->tokenError = 'Token sudah digunakan';
            } elseif (str_contains($message, 'kadaluarsa') || str_contains($message, 'expired')) {
                $this->tokenError = 'Token sudah kadaluarsa (lebih dari 60 menit)';
            } else {
                $this->tokenError = $message;
            }
            
            $this->snackbar = ['type' => 'error', 'message' => $this->tokenError];
        }
    } catch (\Exception $e) {
        $this->tokenError = 'Terjadi kesalahan saat validasi token';
        $this->snackbar = ['type' => 'error', 'message' => $this->tokenError];
    }

    $this->validating = false;
});

$submit = function () {
    // Reset errors
    $this->errors = [];

    // Validasi Password Required
    if (!$this->password) {
        $this->errors['password'] = 'Password wajib diisi';
        $this->snackbar = ['type' => 'error', 'message' => 'Password wajib diisi'];
        return;
    }

    // Validasi Password Minimal 8 Karakter
    if (strlen($this->password) < 8) {
        $this->errors['password'] = 'Password minimal 8 karakter';
        $this->snackbar = ['type' => 'error', 'message' => 'Password minimal 8 karakter'];
        return;
    }

    // Validasi Password Confirmation Required
    if (!$this->password_confirmation) {
        $this->errors['password_confirmation'] = 'Konfirmasi password wajib diisi';
        $this->snackbar = ['type' => 'error', 'message' => 'Konfirmasi password wajib diisi'];
        return;
    }

    // Validasi Password Confirmation Match
    if ($this->password !== $this->password_confirmation) {
        $this->errors['password_confirmation'] = 'Konfirmasi password tidak cocok';
        $this->snackbar = ['type' => 'error', 'message' => 'Konfirmasi password tidak cocok'];
        return;
    }

    $this->loading = true;

    try {
        $response = AuthApiService::resetPassword(
            $this->token,
            $this->password,
            $this->password_confirmation
        );

        if ($response->successful()) {
            $this->success = true;
            $this->snackbar = ['type' => 'success', 'message' => $response->json('message') ?? 'Password berhasil diubah'];
            
            // Redirect ke login setelah 2 detik
            $this->dispatch('redirect-to-login');
        } else {
            $data = $response->json();
            
            // Handle validation errors (422)
            if ($response->status() === 422 && isset($data['errors'])) {
                $errors = $data['errors'];
                
                // Map errors to fields
                foreach ($errors as $field => $messages) {
                    $this->errors[$field] = is_array($messages) ? $messages[0] : $messages;
                }
                
                // Show first error in snackbar
                $firstError = reset($errors);
                $message = is_array($firstError) ? $firstError[0] : $firstError;
            } 
            // Handle token errors (400)
            elseif ($response->status() === 400) {
                $message = $data['message'] ?? 'Token tidak valid';
                
                // Specific token error messages
                if (str_contains($message, 'sudah digunakan')) {
                    $message = 'Token sudah digunakan. Silakan request reset password baru.';
                } elseif (str_contains($message, 'kadaluarsa') || str_contains($message, 'expired')) {
                    $message = 'Token sudah kadaluarsa. Silakan request reset password baru.';
                }
            } else {
                $message = $data['message'] ?? 'Gagal mengubah password';
            }
            
            $this->snackbar = ['type' => 'error', 'message' => $message];
        }
    } catch (\Exception $e) {
        $this->snackbar = ['type' => 'error', 'message' => 'Terjadi kesalahan. Silakan coba lagi.'];
    }

    $this->loading = false;
};
?>

<x-layouts.mobile title="Reset Password">

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
            this._timeout = setTimeout(async () => {
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
    @redirect-to-login.window="setTimeout(() => window.location.href = '/login', 2000)"
    x-show="show" x-cloak x-transition.opacity.duration.300ms
        class="fixed top-0 left-1/2 -translate-x-1/2 w-[390px] z-[9999] flex items-center gap-2 px-4 py-3 text-sm font-medium shadow-lg rounded-b-lg"
        :class="styles[snackbar?.type ?? 'success']">
        <span class="text-lg" x-text="icons[snackbar?.type ?? 'success']"></span>
        <span x-text="snackbar?.message ?? ''"></span>
    </div>

    <div class="relative w-full min-h-screen flex flex-col">

        {{-- Background --}}
        <img src="/images/assets/bg-pattern.png"
            class="absolute inset-0 w-full h-full object-cover pointer-events-none" />

        {{-- Content --}}
        <div class="relative z-10 px-6 pt-10">

            {{-- Logo --}}
            <div class="flex justify-center mt-4 mb-3">
                <img src="/images/assets/logo.png" class="w-20">
            </div>

            {{-- Card --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-6 shadow-lg">

                @if($validating)
                    {{-- Loading State --}}
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto mb-4"></div>
                        <p class="text-gray-600">Memvalidasi token...</p>
                    </div>

                @elseif(!$tokenValid)
                    {{-- Invalid Token State --}}
                    <div class="text-center py-4">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Token Tidak Valid</h2>
                        <p class="text-sm text-gray-600 mb-2">
                            {{ $tokenError }}
                        </p>
                        <p class="text-xs text-gray-500 mb-6">
                            Link reset password tidak valid, sudah digunakan, atau sudah kadaluarsa (lebih dari 60 menit). Silakan request ulang.
                        </p>
                        <a href="/forgot-password" class="block w-full bg-green-600 text-white py-3 rounded-lg font-medium text-center mb-2">
                            Request Reset Password Baru
                        </a>
                        <a href="/login" class="block w-full bg-gray-200 text-gray-700 py-3 rounded-lg font-medium text-center">
                            Kembali ke Login
                        </a>
                    </div>

                @elseif($success)
                    {{-- Success State --}}
                    <div class="text-center py-4">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Password Berhasil Diubah!</h2>
                        <p class="text-sm text-gray-600 mb-6">
                            Password Anda telah berhasil diubah. Silakan login dengan password baru Anda.
                        </p>
                        <p class="text-sm text-gray-500">Mengalihkan ke halaman login...</p>
                    </div>

                @else
                    {{-- Form State --}}
                    <h1 class="text-center font-bold text-xl mb-2">Reset Password</h1>
                    <p class="text-center text-sm text-gray-600 mb-1">
                        Masukkan password baru untuk akun:
                    </p>
                    <p class="text-center text-sm font-semibold text-green-700 mb-6">
                        {{ $email }}
                    </p>

                    {{-- PASSWORD --}}
                    <x-mobile.input 
                        wire:model.defer="password" 
                        placeholder="Password Baru (min. 8 karakter)" 
                        type="password"
                        :disabled="$loading"
                        :invalid="isset($errors['password'])" />
                    
                    @if(isset($errors['password']))
                        <p class="text-red-500 text-xs mt-1 ml-1">{{ $errors['password'] }}</p>
                    @endif

                    {{-- PASSWORD CONFIRMATION --}}
                    <x-mobile.input 
                        wire:model.defer="password_confirmation" 
                        placeholder="Konfirmasi Password Baru" 
                        type="password"
                        :disabled="$loading"
                        :invalid="isset($errors['password_confirmation'])" />
                    
                    @if(isset($errors['password_confirmation']))
                        <p class="text-red-500 text-xs mt-1 ml-1">{{ $errors['password_confirmation'] }}</p>
                    @endif

                    {{-- BUTTON SUBMIT --}}
                    <x-mobile.button 
                        class="mt-4" 
                        wire:click="submit"
                        :disabled="$loading">
                        @if($loading)
                            <span class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        @else
                            Ubah Password
                        @endif
                    </x-mobile.button>

                    {{-- BACK TO LOGIN --}}
                    <p class="text-center text-sm mt-4 text-gray-600">
                        <a href="/login" class="text-green-700 font-semibold">Kembali ke Login</a>
                    </p>
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
                    <h1 class="text-5xl font-extrabold mb-6 leading-tight tracking-tight">Atur Ulang<br>Password Baru</h1>
                    <p class="text-lg text-green-100 mb-10 leading-relaxed font-medium">Buat password baru yang kuat untuk melindungi data base organisasi dan informasi jejaring Anda di KTA Digital Perwakab.</p>
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

                    @if($validating)
                        {{-- Loading State --}}
                        <div class="text-center py-10">
                            <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-green-600 mx-auto mb-6"></div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Memvalidasi Token</h2>
                            <p class="text-gray-500">Mohon tunggu sebentar, kami sedang memeriksa keamanan link Anda...</p>
                        </div>

                    @elseif(!$tokenValid)
                        {{-- Invalid Token State --}}
                        <div class="text-center py-8">
                            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-4">Link Tidak Valid ❌</h2>
                            <p class="text-red-600 font-medium mb-3">{{ $tokenError }}</p>
                            <p class="text-gray-500 mb-8 leading-relaxed">Link reset password tidak valid, mungkin sudah digunakan, atau telah kadaluarsa. Sistem membatasi link hanya valid selama 60 menit.</p>
                            
                            <div class="flex flex-col gap-3">
                                <a href="/forgot-password" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition shadow-lg shadow-green-200 text-center">
                                    Request Reset Password Baru
                                </a>
                                <a href="/login" class="w-full py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition text-center">
                                    Kembali ke Login
                                </a>
                            </div>
                        </div>

                    @elseif($success)
                        {{-- Success State --}}
                        <div class="text-center py-8">
                            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-4">Selesai! 🎉</h2>
                            <p class="text-gray-600 mb-8 leading-relaxed">Password untuk akun <strong>{{ $email }}</strong> telah berhasil diubah secara permanen. Anda otomatis akan dialihkan ke halaman login.</p>
                        </div>

                    @else
                        {{-- Form State --}}
                        <div class="mb-10 text-center lg:text-left">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">Simpan Password Baru 🔑</h2>
                            <p class="text-gray-500 mb-1">Amankan akun Anda dengan password baru.</p>
                            <p class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold border border-green-200">
                                Akun: {{ $email }}
                            </p>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Password Baru <span class="text-red-500">*</span></label>
                                <input wire:model.defer="password" type="password" placeholder="Minimal 8 karakter"
                                    class="w-full px-4 py-3.5 rounded-xl border {{ isset($errors['password']) ? 'border-red-300 ring-2 ring-red-100' : 'border-gray-200 focus:border-green-500 focus:ring-4 focus:ring-green-100' }} transition outline-none text-sm font-medium">
                                @if(isset($errors['password']))<p class="text-red-500 text-xs mt-2 font-medium">{{ $errors['password'] }}</p>@endif
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                                <input wire:model.defer="password_confirmation" type="password" placeholder="Ketik ulang password baru" wire:keydown.enter="submit"
                                    class="w-full px-4 py-3.5 rounded-xl border {{ isset($errors['password_confirmation']) ? 'border-red-300 ring-2 ring-red-100' : 'border-gray-200 focus:border-green-500 focus:ring-4 focus:ring-green-100' }} transition outline-none text-sm font-medium">
                                @if(isset($errors['password_confirmation']))<p class="text-red-500 text-xs mt-2 font-medium">{{ $errors['password_confirmation'] }}</p>@endif
                            </div>

                            <button wire:click="submit" {{ $loading ? 'disabled' : '' }}
                                class="w-full py-4 mt-2 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition shadow-lg shadow-green-200 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                @if($loading)
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                    Menyimpan...
                                @else
                                    Ubah Password Permanen
                                @endif
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-slot:desktop>

</x-layouts.mobile>
