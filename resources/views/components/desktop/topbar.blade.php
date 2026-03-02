@props(['title' => 'Beranda'])

<header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-30">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <h2 class="text-xl font-bold text-gray-800 tracking-tight">
                {{ $title }}
            </h2>
            
            @if(session('user'))
            <div class="flex items-center space-x-4">
                <a href="{{ route('mobile.poin.index') }}" class="flex items-center px-4 py-2 bg-yellow-50 rounded-full border border-yellow-100 hover:bg-yellow-100 transition">
                    <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-bold text-yellow-700">{{ session('user.points') ?? 0 }} Pts</span>
                </a>
                
                <div class="flex items-center pl-4 border-l border-gray-200">
                    <img class="h-9 w-9 rounded-full object-cover border-2 border-green-500 shadow-sm" 
                         src="{{ api_profile_url(session('user.profile_photo') ?? null) }}" 
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(session('user.name') ?? 'User') }}&background=random'"
                         alt="{{ session('user.name') ?? 'User' }}">
                    <span class="ml-3 text-sm font-medium text-gray-700 hidden lg:block">
                        {{ session('user.name') ?? 'Pengguna' }}
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>
</header>
