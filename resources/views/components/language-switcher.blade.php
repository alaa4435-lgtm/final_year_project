{{--
    Language switcher: segmented control (EN | العربية).
    Usage: <x-language-switcher />
    Requires the route: language.switch ({locale}) and the translation keys
    auth.language, auth.english, auth.arabic.
--}}

@php
    $current = app()->getLocale();

    $languages = [
        'en' => ['label' => __('auth.english'), 'lang' => 'en'],
        'ar' => ['label' => __('auth.arabic'),  'lang' => 'ar'],
    ];
@endphp

<nav
    aria-label="{{ __('auth.language') }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 p-0.5']) }}
>
    @foreach ($languages as $locale => $language)
        @php $active = $current === $locale; @endphp

        <a
            href="{{ route('language.switch', ['locale' => $locale]) }}"
            lang="{{ $language['lang'] }}"
            hreflang="{{ $language['lang'] }}"
            @if ($active) aria-current="true" @endif
            class="rounded-md px-3 py-1.5 text-[13px] font-medium leading-none transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#935073]/40
                {{ $active
                    ? 'bg-[#502D55] text-[#F8F4E9] shadow-sm'
                    : 'text-gray-600 hover:text-[#502D55]' }}"
        >
            {{ $language['label'] }}
        </a>
    @endforeach
</nav>