<x-guest-layout>

    <h2 class="text-2xl font-extrabold text-gray-900">
        {{ __('auth.password_confirm') }}
    </h2>

    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
        {{ __('auth.confirm_password_message') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-8 space-y-5">
        @csrf

        {{-- Password --}}
        <div>
            <x-input-label for="password" :value="__('auth.password_label')" class="mb-1.5" />

            <x-auth-input
                id="password"
                type="password"
                name="password"
                required
                autofocus
                autocomplete="current-password"
                placeholder="••••••••"
                icon="lock"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <x-primary-button
            class="btn-primary w-full py-3 rounded-lg font-semibold text-sm shadow-lg shadow-purple-900/10"
        >
            {{ __('auth.confirm') }}
        </x-primary-button>

    </form>

</x-guest-layout>