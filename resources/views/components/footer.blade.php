
<footer class="mt-auto bg-[#2A1630] text-[#F6DBC0]/70">
    <div class="mx-auto w-full max-w-6xl px-6 py-14 sm:px-10">

        <div class="grid gap-10 md:grid-cols-[2fr_1fr_1fr]">

            {{-- Brand --}}
            <div class="max-w-xs">
                <a
                    href="{{ url('/') }}"
                    class="inline-flex text-[#F8F4E9]"
                    aria-label="{{ config('app.name', 'Aurie') }}"
                >
                    <x-application-logo />
                </a>

                <p class="mt-4 text-sm leading-relaxed">
                    {{ __('footer.description') }}
                </p>
            </div>

            {{-- Platform --}}
            <nav aria-label="{{ __('footer.platform') }}">
                <h2 class="text-sm font-semibold text-[#F8F4E9]">
                    {{ __('footer.platform') }}
                </h2>

                <ul class="mt-4 space-y-3 text-sm">
                    <li><a href="{{ url('/') }}" class="transition-colors hover:text-[#F8F4E9]">{{ __('footer.home') }}</a></li>
                    <li><a href="{{ route('register') }}" class="transition-colors hover:text-[#F8F4E9]">{{ __('footer.get_started') }}</a></li>
                    <li><a href="{{ route('login') }}" class="transition-colors hover:text-[#F8F4E9]">{{ __('footer.log_in') }}</a></li>
                </ul>
            </nav>

            {{-- Legal --}}
            <nav aria-label="{{ __('footer.legal') }}">
                <h2 class="text-sm font-semibold text-[#F8F4E9]">
                    {{ __('footer.legal') }}
                </h2>

                <ul class="mt-4 space-y-3 text-sm">
                    <li><a href="{{ route('terms') }}" class="transition-colors hover:text-[#F8F4E9]">{{ __('terms.terms_title') }}</a></li>
                    <li><a href="{{ route('privacy') }}" class="transition-colors hover:text-[#F8F4E9]">{{ __('privacy.privacy_title') }}</a></li>
                </ul>
            </nav>

        </div>

        {{-- Bottom bar --}}
        <div class="mt-12 border-t border-white/10 pt-6 text-xs">
            &copy; {{ date('Y') }} {{ config('app.name', 'Aurie') }}. {{ __('footer.rights') }}
        </div>

    </div>
</footer>