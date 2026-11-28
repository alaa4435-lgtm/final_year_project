@php
    $switchRoute = Route::has('language.switch')
        ? 'language.switch'
        : (Route::has('locale.switch') ? 'locale.switch' : null);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') · Aurie</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Newsreader:wght@400;500;600&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { deep: '#502D55', mauve: '#935073', peach: '#F6DBC0', ink: '#0F172A' },
                    fontFamily: {
                        serif: ['Newsreader', 'Georgia', 'serif'],
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <style>
        html[lang="ar"] body,
        html[lang="ar"] .font-serif { font-family: 'Tajawal', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[#FAFAFA] font-sans text-slate-600 antialiased">

<header class="bg-white border-b border-slate-100">
    <div class="mx-auto flex h-14 max-w-6xl items-center justify-between px-6">
        <div class="flex items-center gap-4">
            @hasSection('back')
                <a href="@yield('back')" aria-label="{{ __('project.back') }}" class="text-slate-400 hover:text-deep">
                    <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                    </svg>
                </a>
            @endif
            <a href="{{ auth()->user()?->dashboardUrl() ?? route('welcome') }}" class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-deep text-white">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/><line x1="14" y1="4" x2="10" y2="20"/>
                    </svg>
                </span>
                <span class="font-serif text-xl font-semibold text-deep">Aurie</span>
            </a>
        </div>

        <div class="flex items-center gap-5">
            @if ($switchRoute)
                <div class="flex items-center gap-2 text-xs">
                    <a href="{{ route($switchRoute, 'en') }}"
                       class="{{ app()->getLocale() === 'en' ? 'font-bold text-deep' : 'text-slate-400 hover:text-slate-600' }}">EN</a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route($switchRoute, 'ar') }}"
                       class="{{ app()->getLocale() === 'ar' ? 'font-bold text-deep' : 'text-slate-400 hover:text-slate-600' }}">عربي</a>
                </div>
            @endif

            @yield('header_end')

            @auth
                <details class="relative">
                    <summary class="flex cursor-pointer list-none items-center [&::-webkit-details-marker]:hidden">
                        <x-avatar :user="auth()->user()" size="h-9 w-9" />
                    </summary>
                    <div class="absolute end-0 z-20 mt-2 w-56 rounded-2xl border border-slate-100 bg-white p-2 text-sm shadow-lg">
                        <div class="truncate px-3 py-2 text-xs text-slate-400">{{ auth()->user()->email }}</div>
                        <a href="{{ auth()->user()->dashboardUrl() }}" class="block rounded-xl px-3 py-2 hover:bg-slate-50">{{ __('nav.dashboard') }}</a>
                        @if (auth()->user()->isLearner())
                            <a href="{{ route('projects.index') }}" class="block rounded-xl px-3 py-2 hover:bg-slate-50">{{ __('nav.projects') }}</a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2 hover:bg-slate-50">{{ __('nav.profile') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full rounded-xl px-3 py-2 text-start text-red-600 hover:bg-red-50">{{ __('nav.logout') }}</button>
                        </form>
                    </div>
                </details>
            @endauth
        </div>
    </div>
</header>

<main class="flex-1">
    @yield('content')
</main>

</body>
</html>