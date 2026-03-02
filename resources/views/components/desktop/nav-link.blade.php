@props(['active', 'route', 'icon', 'logout' => false])

@php
$isActive = in_array(request()->route()->getName(), [
    $route, 
    $route . '.index', 
    $route . '.show', 
    $route . '.create', 
    $route . '.edit'
]) || request()->routeIs(explode('.', $route)[0] . '.*');
// custom logic for specific active states
if ($active === 'home') $isActive = request()->routeIs('mobile.home');
if ($active === 'kta') $isActive = request()->routeIs('mobile.kta');
if ($active === 'articles') $isActive = request()->routeIs('mobile.article*');
if ($active === 'info-duka') $isActive = request()->routeIs('mobile.info-duka*');
if ($active === 'struktur') $isActive = request()->routeIs('mobile.struktur-organisasi');
if ($active === 'karya') $isActive = request()->routeIs('mobile.karya*');
if ($active === 'marketplace') $isActive = request()->routeIs('mobile.marketplace*') || request()->routeIs('mobile.my-products*');
if ($active === 'bisnis') $isActive = request()->routeIs('mobile.bisnis*');
if ($active === 'donation') $isActive = request()->routeIs('mobile.donation*');
if ($active === 'poin') $isActive = request()->routeIs('mobile.poin*');
if ($active === 'profile') $isActive = request()->routeIs('mobile.profile*');

$classes = $isActive
            ? 'group flex items-center px-4 py-3 text-sm font-semibold rounded-xl bg-green-50 text-green-700 transition duration-200'
            : 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl text-gray-600 hover:bg-gray-50 hover:text-green-700 transition duration-200';
@endphp

@if($logout)
    <a href="{{ route($route) }}" {{ $attributes->merge(['class' => $classes]) }}>
        <svg class="w-5 h-5 mr-3 {{ $isActive ? 'text-green-700' : 'text-gray-400 group-hover:text-green-700' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
        </svg>
        {{ $slot }}
    </a>
@else
    <a href="{{ route($route) }}" {{ $attributes->merge(['class' => $classes]) }}>
        <svg class="w-5 h-5 mr-3 {{ $isActive ? 'text-green-700' : 'text-gray-400 group-hover:text-green-700' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
        </svg>
        {{ $slot }}
    </a>
@endif
