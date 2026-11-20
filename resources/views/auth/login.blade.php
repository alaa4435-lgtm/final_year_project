<x-guest-layout>


<h2 class="mt-8 text-2xl font-extrabold tracking-tight text-gray-900">
    {{ __('auth.login_into_app', ['app' => config('app.name', 'Aurie')]) }}
</h2>

<x-auth-session-status class="mt-3" :status="session('status')"/>

<form method="POST" action="{{ route('login') }}" class="mt-4 space-y-5">
    @csrf

    {{-- Email --}}
    <div>
        <x-input-label
            for="email"
            :value="__('auth.email_address')"
            class="mb-1.5"
        />

        <div class="input-field flex items-center gap-2 rounded-lg bg-white px-3 py-2.5">

            <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#9CA3AF"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="shrink-0"
            >
                <path d="m22 6-10 7L2 6"/>
                <rect x="2" y="4" width="20" height="16" rx="2"/>
            </svg>

            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                :placeholder="__('auth.email_placeholder')"
                class="w-full border-0 bg-transparent p-0 text-sm outline-none focus:ring-0 placeholder:text-gray-400"
            />

        </div>

        <x-input-error
            :messages="$errors->get('email')"
            class="mt-1.5"
        />
    </div>


    {{-- Password --}}
    <div class="mt-5">

        <x-input-label
            for="password"
            :value="__('auth.password')"
            class="mb-1.5"
        />

        <div class="input-field flex items-center gap-2 rounded-lg bg-white px-3 py-2.5">

            <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#9CA3AF"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="shrink-0"
            >
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>

            <x-text-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                :placeholder="__('auth.password_placeholder')"
                class="w-full border-0 bg-transparent p-0 text-sm outline-none focus:ring-0 placeholder:text-gray-400"
            />

        </div>

        <x-input-error
            :messages="$errors->get('password')"
            class="mt-1.5"
        />

        {{-- Forgot Password --}}
        @if (Route::has('password.request'))
            <div class="mt-2 text-right">
                <a
                    href="{{ route('password.request') }}"
                    class="text-xs font-medium text-brand hover:underline"
                >
                    {{ __('auth.forgot_password') }}
                </a>
            </div>
        @endif

    </div>

    {{-- Sign In --}}
    <x-primary-button class="btn-primary mt-5 w-full justify-center rounded-lg py-3 text-sm font-semibold shadow-lg shadow-purple-900/10">
        {{ __('auth.sign_in') }}
    </x-primary-button>

</form>


{{-- Divider --}}
<div class="relative my-6 flex items-center justify-center">

    <div class="absolute inset-0 flex items-center" aria-hidden="true">
        <div class="w-full border-t border-gray-200"></div>
    </div>

    <span class="relative bg-white px-4 text-[11px] font-semibold uppercase tracking-wider text-gray-400">
        {{ __('auth.or_continue_with') }}
    </span>

</div>


{{-- Google --}}
<a
    href="{{ route('auth.google') }}"
    class="flex w-full items-center justify-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-[#935073]/20"
>
    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
    </svg>

    {{ __('auth.continue_with_google') }}

</a>


{{-- Register --}}
<p class="mt-8 text-center text-sm text-gray-500">
    {{ __('auth.dont_have_account') }}

    <a
        href="{{ route('register') }}"
        class="font-semibold text-brand hover:underline"
    >
        {{ __('auth.sign_up_free') }}
    </a>
</p>


</x-guest-layout>
