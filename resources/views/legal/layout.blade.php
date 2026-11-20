{{--
    Legal pages layout (Terms of Service / Privacy Policy).
    Child views keep the same sections:
        @section('title')   @section('updated')   @section('content')
    Inside @section('content') use plain h2 / h3 / p / ul / ol / li / a:
    they are styled here, so the content files need no extra classes.
--}}

<x-guest-layout>

    {{-- Replaces the top-bar "Back to Home" button on legal pages --}}
    <x-slot name="back">
        <x-back-home :href="route('register')" :label="__('terms.back_to_register')" />
    </x-slot>

    <article class="w-full text-start text-gray-700">

        {{-- Document switcher --}}
        <nav
            aria-label="{{ __('terms.terms_title') }} / {{ __('privacy.privacy_title') }}"
            class="inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 p-0.5"
        >
            @foreach ([
                'terms'   => __('terms.terms_title'),
                'privacy' => __('privacy.privacy_title'),
            ] as $routeName => $label)
                @php $active = request()->routeIs($routeName); @endphp

                <a
                    href="{{ route($routeName) }}"
                    @if ($active) aria-current="page" @endif
                    class="rounded-md px-3.5 py-1.5 text-[13px] font-medium leading-none transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#935073]/40
                        {{ $active
                            ? 'bg-[#502D55] text-[#F8F4E9] shadow-sm'
                            : 'text-gray-600 hover:text-[#502D55]' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        {{-- Title block --}}
        <header class="mt-8 border-b border-gray-200 pb-6">
            <h1 class="text-3xl font-bold tracking-tight text-[#2A1630]">
                @yield('title')
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                {{ __('terms.last_updated') }}: @yield('updated')
            </p>
        </header>

        {{-- Document body: styles every element used by the legal content --}}
        <div
            class="mt-8 space-y-10 text-[15px] leading-7
                   [&_section]:space-y-3
                   [&_h2]:text-lg [&_h2]:font-semibold [&_h2]:leading-snug [&_h2]:text-[#2A1630]
                   [&_h3]:text-base [&_h3]:font-semibold [&_h3]:text-[#2A1630]
                   [&_p]:text-gray-600
                   [&_ul]:list-disc [&_ul]:space-y-1.5 [&_ul]:ps-6 [&_ul]:text-gray-600
                   [&_ol]:list-decimal [&_ol]:space-y-1.5 [&_ol]:ps-6 [&_ol]:text-gray-600
                   [&_li]:ps-1
                   [&_strong]:font-semibold [&_strong]:text-gray-800
                   [&_a]:font-medium [&_a]:text-[#935073] [&_a]:underline [&_a]:underline-offset-2 hover:[&_a]:text-[#502D55]"
        >
            @yield('content')
        </div>

    </article>

</x-guest-layout>