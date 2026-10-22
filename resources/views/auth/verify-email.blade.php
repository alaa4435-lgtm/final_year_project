<x-guest-layout>

    <div class="mt-8">

        <div class="text-center">

            <div
                class="mx-auto w-14 h-14 rounded-full flex items-center justify-center"
                style="background: rgba(147, 80, 115, 0.10); color: var(--mauve);"
            >
                <svg
                    width="26"
                    height="26"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M4 4h16v16H4z" stroke="none"/>
                    <path d="m22 6-10 7L2 6"/>
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                </svg>
            </div>

            <h2 class="mt-5 text-2xl font-extrabold text-gray-900">
                {{ __('auth.verify_email') }}
            </h2>

            <p class="mt-3 text-sm text-gray-500 leading-relaxed">
                {{ __('auth.verify_email_description') }}
            </p>

            <p class="mt-3 text-sm text-gray-500 leading-relaxed">
                {{ __('auth.verify_email_resend_description') }}
            </p>

        </div>

        <x-auth-session-status
            class="mt-6"
            :status="session('status')"
        />

        <div class="mt-8 space-y-3">

            {{-- Resend verification --}}
            <form
                method="POST"
                action="{{ route('verification.send') }}"
            >
                @csrf

                <x-primary-button
                    class="btn-primary w-full py-3 rounded-lg font-semibold text-sm shadow-lg shadow-purple-900/10"
                >
                    {{ __('auth.resend_verification_email') }}
                </x-primary-button>
            </form>

        </div>

    </div>

</x-guest-layout>
