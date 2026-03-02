<?php
use function Livewire\Volt\{state};
use App\Services\UserApiService;

state([
    'user' => session('user') ?? [],
    'family_members' => session('user.family_members') ?? session('user.familyMembers') ?? [],

    // Modal states
    'showModal' => false,
    'isEdit' => false,
    'deleteId' => null,
    'showDeleteModal' => false,

    // Form fields
    'member_id' => null,
    'relationship' => '',
    'name_ktp' => '',
    'nickname' => '',
    'birth_date' => '',
    'address' => '',

    'snackbar' => ['type' => '', 'message' => ''],
]);

$refreshUser = function () {
    $token = session('token');
    if (!$token) return;

    $me = UserApiService::me($token);
    if ($me->successful()) {
        $freshUser = $me->json('data');
        session(['user' => $freshUser]);
        $this->user = $freshUser;
        
        $this->family_members = $freshUser['family_members'] ?? $freshUser['familyMembers'] ?? [];
    }
};

$openAddModal = function () {
    $this->resetForm();
    $this->isEdit = false;
    $this->showModal = true;
};

$openEditModal = function ($index) {
    $this->resetForm();
    $member = $this->family_members[$index];
    $this->member_id = $member['id'] ?? null;
    $this->relationship = $member['relationship'] ?? '';
    $this->name_ktp = $member['name_ktp'] ?? '';
    $this->nickname = $member['nickname'] ?? '';
    
    if (!empty($member['birth_date'])) {
        $this->birth_date = substr($member['birth_date'], 0, 10);
    } else {
        $this->birth_date = '';
    }
    
    $this->address = $member['address'] ?? '';
    $this->isEdit = true;
    $this->showModal = true;
};

$resetForm = function () {
    $this->member_id = null;
    $this->relationship = '';
    $this->name_ktp = '';
    $this->nickname = '';
    $this->birth_date = '';
    $this->address = '';
};

$submit = function () {
    $token = session('token');
    if (!$token) {
        $this->snackbar = ['type' => 'error', 'message' => 'Sesi tidak valid'];
        return;
    }

    $payload = [
        'relationship' => $this->relationship,
        'name_ktp' => $this->name_ktp,
        'nickname' => $this->nickname,
        'birth_date' => $this->birth_date ?: null,
        'address' => $this->address ?: null,
    ];

    if ($this->isEdit && $this->member_id) {
        $response = UserApiService::updateFamilyMember($token, $this->member_id, $payload);
        $successMsg = 'Anggota keluarga berhasil diperbarui';
    } else {
        $response = UserApiService::storeFamilyMember($token, $payload);
        $successMsg = 'Anggota keluarga berhasil ditambahkan';
    }

    if ($response->successful()) {
        $this->snackbar = ['type' => 'success', 'message' => $successMsg];
        $this->showModal = false;
        $this->refreshUser();
    } else {
        $this->snackbar = ['type' => 'error', 'message' => $response->json('message') ?? 'Terjadi kesalahan'];
    }
};

$confirmDelete = function ($id) {
    if (!$id) return;
    $this->deleteId = $id;
    $this->showDeleteModal = true;
};

$deleteMember = function () {
    $token = session('token');
    if (!$token || !$this->deleteId) return;

    $response = UserApiService::deleteFamilyMember($token, $this->deleteId);

    if ($response->successful()) {
        $this->snackbar = ['type' => 'success', 'message' => 'Anggota keluarga berhasil dihapus'];
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->refreshUser();
    } else {
        $this->snackbar = ['type' => 'error', 'message' => $response->json('message') ?? 'Gagal menghapus data'];
    }
};

?>

<x-layouts.mobile title="Data Keluarga">

    {{-- SNACKBAR --}}
    @if($snackbar['message'])
        <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[390px]
                {{ $snackbar['type'] === 'error' ? 'bg-red-500' : 'bg-green-600' }}
                text-white px-4 py-3 text-sm font-medium shadow-lg rounded-b-lg z-[9999]"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            {{ $snackbar['message'] }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="w-full bg-green-600 px-4 py-4 flex items-center space-x-3 rounded-b-2xl">
        <button onclick="window.history.back()">
            <img src="/images/assets/icon/back.svg" class="w-5 h-5">
        </button>
        <p class="text-white font-semibold text-base">Data Keluarga</p>
    </div>

    <div class="px-4 pb-20 mt-4 space-y-6">
        
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Keluarga</h2>
            <button wire:click="openAddModal" class="bg-green-600 text-white text-sm px-3 py-1.5 rounded-lg font-medium">
                + Tambah
            </button>
        </div>

        <div class="space-y-4">
            @forelse($family_members as $index => $member)
                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm relative">
                    <div class="absolute top-4 right-4 flex space-x-2">
                        <button wire:click="openEditModal({{ $index }})" class="text-blue-500 text-xs font-medium bg-blue-50 px-2 py-1 rounded">Edit</button>
                        <button wire:click="confirmDelete('{{ $member['id'] ?? '' }}')" class="text-red-500 text-xs font-medium bg-red-50 px-2 py-1 rounded">Hapus</button>
                    </div>

                    <p class="text-base font-semibold text-gray-800 pr-24">{{ $member['name_ktp'] ?? '-' }}</p>
                    <p class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded inline-block mt-1 mb-3">{{ $member['relationship'] ?? '-' }}</p>
                    
                    <div class="text-sm text-gray-600 space-y-1">
                        @if(!empty($member['nickname']))
                            <p><span class="opacity-70">Panggilan:</span> {{ $member['nickname'] }}</p>
                        @endif
                        @if(!empty($member['birth_date']))
                            <p><span class="opacity-70">Tgl Lahir:</span> {{ date('d M Y', strtotime($member['birth_date'])) }}</p>
                        @elseif(!empty($member['age']))
                            <p><span class="opacity-70">Umur:</span> {{ $member['age'] }} tahun</p>
                        @endif
                        @if(!empty($member['address']))
                            <p><span class="opacity-70">Domisili:</span> {{ $member['address'] }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <p class="text-gray-500 text-sm">Belum ada data keluarga</p>
                </div>
            @endforelse
        </div>

    </div>

    {{-- MODAL FORM --}}
    @if($showModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 px-4">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">{{ $isEdit ? 'Edit Keluarga' : 'Tambah Keluarga' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-500 text-2xl font-bold">&times;</button>
            </div>

            <form wire:submit.prevent="submit" class="space-y-4 text-left">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hubungan <span class="text-red-500">*</span></label>
                    <select wire:model.defer="relationship" required class="w-full rounded-lg px-3 py-2 text-sm border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                        <option value="">Pilih Hubungan</option>
                        <option value="Ayah">Ayah</option>
                        <option value="Ibu">Ibu</option>
                        <option value="Suami">Suami</option>
                        <option value="Istri">Istri</option>
                        <option value="Anak">Anak</option>
                        <option value="Ayah Mertua">Ayah Mertua</option>
                        <option value="Ibu Mertua">Ibu Mertua</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sesuai KTP <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.defer="name_ktp" required placeholder="Masukkan nama" class="w-full rounded-lg px-3 py-2 text-sm border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Panggilan</label>
                    <input type="text" wire:model.defer="nickname" placeholder="Masukkan nama panggilan" class="w-full rounded-lg px-3 py-2 text-sm border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" wire:model.defer="birth_date" class="w-full rounded-lg px-3 py-2 text-sm border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Domisi</label>
                    <textarea wire:model.defer="address" rows="2" placeholder="Masukkan Domisili" class="w-full rounded-lg px-3 py-2 text-sm border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none resize-none"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-green-600 text-white font-semibold py-2.5 rounded-xl">
                        Simpan
                    </button>
                    <button type="button" wire:click="$set('showModal', false)" class="mt-2 w-full bg-gray-100 text-gray-700 font-semibold py-2.5 rounded-xl">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- MODAL DELETE --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 px-4">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center">
            <div class="mx-auto mb-3 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Hapus Data</h3>
            <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus anggota keluarga ini?</p>
            
            <div class="flex space-x-3">
                <button wire:click="$set('showDeleteModal', false)" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl font-medium">
                    Batal
                </button>
                <button wire:click="deleteMember" class="flex-1 bg-red-500 text-white py-2.5 rounded-xl font-medium">
                    Hapus
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- ================== DESKTOP VIEW ================== -->
    <x-slot:desktop>
        <x-desktop.layout title="Data Keluarga">
            <div class="max-w-4xl mx-auto">
                {{-- HEADER / TITLE --}}
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <a href="{{ route('mobile.profile') }}" class="hover:text-green-600 transition">&larr; Kembali ke Profil</a>
                </div>

                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Data Keluarga</h1>
                        <p class="text-gray-500 mt-1">Kelola data anggota keluarga atau tertanggung Anda.</p>
                    </div>
                    <button wire:click="openAddModal" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-md shadow-green-200 transition">
                        + Tambah Keluarga
                    </button>
                </div>

                {{-- LIST KELUARGA --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($family_members as $index => $member)
                        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative group hover:shadow-md transition">
                            <div class="absolute top-6 right-6 flex space-x-2 opacity-0 group-hover:opacity-100 transition">
                                <button wire:click="openEditModal({{ $index }})" class="text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm font-semibold transition">Edit</button>
                                <button wire:click="confirmDelete('{{ $member['id'] ?? '' }}')" class="text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-sm font-semibold transition">Hapus</button>
                            </div>

                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center font-bold text-lg">
                                    {{ substr($member['name_ktp'] ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $member['name_ktp'] ?? '-' }}</h3>
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full mt-1">{{ $member['relationship'] ?? '-' }}</span>
                                </div>
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-600 mt-4 pt-4 border-t border-gray-50">
                                @if(!empty($member['nickname']))
                                    <p class="flex justify-between"><span class="text-gray-400">Nama Panggilan</span> <span class="font-medium text-gray-800">{{ $member['nickname'] }}</span></p>
                                @endif
                                @if(!empty($member['birth_date']))
                                    <p class="flex justify-between"><span class="text-gray-400">Tanggal Lahir</span> <span class="font-medium text-gray-800">{{ date('d M Y', strtotime($member['birth_date'])) }}</span></p>
                                @elseif(!empty($member['age']))
                                    <p class="flex justify-between"><span class="text-gray-400">Umur</span> <span class="font-medium text-gray-800">{{ $member['age'] }} Tahun</span></p>
                                @endif
                                @if(!empty($member['address']))
                                    <p class="flex justify-between text-right"><span class="text-gray-400 text-left">Domisili</span> <span class="font-medium text-gray-800">{{ $member['address'] }}</span></p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-1 md:col-span-2 text-center py-16 bg-white rounded-2xl border border-dashed border-gray-200">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada data keluarga</h3>
                            <p class="text-gray-500 mb-6">Tambahkan anggota keluarga atau tertanggung Anda di sini.</p>
                            <button wire:click="openAddModal" class="bg-gray-900 hover:bg-black text-white px-6 py-2.5 rounded-xl font-semibold transition">
                                Tambah Keluarga Sekarang
                            </button>
                        </div>
                    @endforelse
                </div>

                {{-- MODALS BUAT DESKTOP (modal mobile ada di slot yang hidden di desktop) --}}
                @if($showModal)
                <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
                    <div class="bg-white rounded-2xl w-full max-w-lg p-8 shadow-2xl relative">
                        <button wire:click="$set('showModal', false)" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        
                        <div class="my-6">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $isEdit ? 'Edit Data Keluarga' : 'Tambah Keluarga Baru' }}</h3>
                            <p class="text-gray-500 mt-1">Lengkapi informasi anggota keluarga di bawah ini.</p>
                        </div>

                        <form wire:submit.prevent="submit" class="space-y-5 text-left">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Sesuai KTP <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model.defer="name_ktp" required placeholder="Masukkan nama" class="w-full rounded-xl px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Hubungan <span class="text-red-500">*</span></label>
                                    <select wire:model.defer="relationship" required class="w-full rounded-xl px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition">
                                        <option value="">Pilih Hubungan</option>
                                        <option value="Ayah">Ayah</option>
                                        <option value="Ibu">Ibu</option>
                                        <option value="Suami">Suami</option>
                                        <option value="Istri">Istri</option>
                                        <option value="Anak">Anak</option>
                                        <option value="Ayah Mertua">Ayah Mertua</option>
                                        <option value="Ibu Mertua">Ibu Mertua</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
                                    <input type="date" wire:model.defer="birth_date" class="w-full rounded-xl px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Panggilan</label>
                                    <input type="text" wire:model.defer="nickname" placeholder="Nama panggilan" class="w-full rounded-xl px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition">
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Domisili</label>
                                    <textarea wire:model.defer="address" rows="2" placeholder="Alamat domisili saat ini" class="w-full rounded-xl px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none resize-none transition"></textarea>
                                </div>
                            </div>

                            <div class="pt-4 flex gap-3">
                                <button type="button" wire:click="$set('showModal', false)" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl transition">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl shadow-md shadow-green-200 transition">
                                    Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                @if($showDeleteModal)
                <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
                    <div class="bg-white rounded-2xl w-full max-w-sm p-8 text-center shadow-2xl">
                        <div class="mx-auto mb-4 w-16 h-16 rounded-full bg-red-50 flex items-center justify-center">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Data?</h3>
                        <p class="text-gray-500 mb-8">Data anggota yang dihapus tidak dapat dikembalikan.</p>
                        
                        <div class="flex space-x-3">
                            <button wire:click="$set('showDeleteModal', false)" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-3 rounded-xl font-semibold transition">
                                Batal
                            </button>
                            <button wire:click="deleteMember" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-semibold shadow-md shadow-red-200 transition">
                                Ya, Hapus
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </x-desktop.layout>
    </x-slot:desktop>

</x-layouts.mobile>
