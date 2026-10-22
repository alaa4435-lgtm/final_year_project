<x-guest-layout>

    <h2 class="text-2xl font-extrabold text-gray-900">
        {{ __('auth.reset_password') }}
    </h2>

    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
        {{ __('auth.reset_password_description') }}
    </p>

    <form
        method="POST"
        action="{{ route('password.store') }}"
        class="mt-8 space-y-5"
    >
        @csrf

        <input
            type="hidden"
            name="token"
            value="{{ $request->route('token') }}"
        >

        {{-- Email --}}
        <div>
            <x-input-label
                for="email"
                :value="__('auth.email_address')"
                class="mb-1.5"
            />

            <x-auth-input
                id="email"
                type="email"
                name="email"
                :value="old('email', $request->email)"
                required
                autofocus
                autocomplete="username"
                placeholder="you@example.com"
                icon="email"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-1.5"
            />
        </div>

        {{-- Password --}}
        <div>
            <x-input-label
                for="password"
                :value="__('auth.new_password')"
                class="mb-1.5"
            />

            <x-auth-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="••••••••"
                icon="password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-1.5"
            />
        </div>

        {{-- Confirm Password --}}
        <div>
            <x-input-label
                for="password_confirmation"
                :value="__('auth.confirm_new_password')"
                class="mb-1.5"
            />

            <x-auth-input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="••••••••"
                icon="password"
            />

            <x-input-error
                :messages="$errors->get('password_confirmation')"
                class="mt-1.5"
            />
        </div>

        <x-primary-button
            class="btn-primary w-full py-3 rounded-lg font-semibold text-sm shadow-lg shadow-purple-900/10"
        >
            {{ __('auth.reset_password_button') }}
        </x-primary-button>

    </form>

</x-guest-layout>

