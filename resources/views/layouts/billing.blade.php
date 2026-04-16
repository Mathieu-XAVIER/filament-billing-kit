<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} — {{ trans('filament-billing-kit::filament-billing-kit.layout.page_title') }}</title>

    @vite(['resources/css/app.css'])

    @filamentStyles
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 antialiased">

<header class="fbk-header" role="banner">
    <div class="fbk-header__inner">
        <div class="fbk-header__brand">
            <svg class="fbk-header__logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M2.5 4A1.5 1.5 0 001 5.5V6h18v-.5A1.5 1.5 0 0017.5 4h-15zM19 8.5H1v6A1.5 1.5 0 002.5 16h15a1.5 1.5 0 001.5-1.5v-6zM3 13.25a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5h-1.5a.75.75 0 01-.75-.75zm4.75-.75a.75.75 0 000 1.5h3.5a.75.75 0 000-1.5h-3.5z" clip-rule="evenodd"/>
            </svg>
            <span class="fbk-header__title">{{ trans('filament-billing-kit::filament-billing-kit.layout.brand') }}</span>
        </div>

        <nav class="fbk-nav" aria-label="{{ trans('filament-billing-kit::filament-billing-kit.layout.nav.aria_label') }}">
            <a href="{{ route('billing.index') }}"
               class="fbk-nav__link {{ request()->routeIs('billing.index') ? 'fbk-nav__link--active' : '' }}">
                {{ trans('filament-billing-kit::filament-billing-kit.layout.nav.plans') }}
            </a>
            <a href="{{ route('billing.subscription') }}"
               class="fbk-nav__link {{ request()->routeIs('billing.subscription') ? 'fbk-nav__link--active' : '' }}">
                {{ trans('filament-billing-kit::filament-billing-kit.layout.nav.subscription') }}
            </a>
            @if(config('filament-billing-kit.enable_invoices', true))
                <a href="{{ route('billing.invoices') }}"
                   class="fbk-nav__link {{ request()->routeIs('billing.invoices') ? 'fbk-nav__link--active' : '' }}">
                    {{ trans('filament-billing-kit::filament-billing-kit.layout.nav.invoices') }}
                </a>
            @endif
        </nav>
    </div>
</header>

<main role="main" class="fbk-main">
    <div class="fbk-container">
        {{ $slot }}
    </div>
</main>

@livewireScripts

</body>

</html>
