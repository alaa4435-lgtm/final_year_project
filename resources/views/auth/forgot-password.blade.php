<x-guest-layout>

    <h2 class="text-2xl font-extrabold text-gray-900">
        {{ __('auth.forgot_password') }}
    </h2>

    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
        {{ __('auth.forgot_password_description') }}
    </p>

    <x-auth-session-status class="mt-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('auth.email_address')" class="mb-1.5" />

            <x-auth-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="you@example.com"
                icon="email"
            />

            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <x-primary-button
            class="btn-primary w-full py-3 rounded-lg font-semibold text-sm shadow-lg shadow-purple-900/10"
        >
            {{ __('auth.send_password_reset_link') }}
        </x-primary-button>

    </form>

    <p class="mt-6 text-sm text-center text-gray-500">
        {{ __('auth.remember_password') }}

        <a href="{{ route('login') }}" class="font-semibold text-brand hover:underline">
            {{ __('auth.login') }}
        </a>
    </p>

</x-guest-layout>
