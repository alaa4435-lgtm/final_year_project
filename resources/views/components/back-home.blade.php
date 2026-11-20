{{--
    Back-to-home link. Works in English (LTR) and Arabic (RTL).
    Usage: <x-back-home />
           <x-back-home :href="route('register')" :label="__('terms.back_to_register')" />
    Default label key: auth.back_to_home

    The arrow comes AFTER the text and points away from it:
      - English: "Back to Home →"
      - Arabic : "العودة" then ←
    The direction is chosen in PHP (no rotate classes), so it cannot disappear.
--}}

@props(['href' => null, 'label' => null])

@php $isRtl = app()->getLocale() === 'ar'; @endphp

<a
    href="{{ $href ?? url('/') }}"
    {{ $attributes->merge([
        'class' => 'group inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-[13px] font-medium leading-none text-gray-700 transition-colors hover:border-[#935073]/50 hover:text-[#502D55] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#935073]/40',
    ]) }}
>
    <span>{{ $label ?? __('auth.back_to_home') }}</span>

    <svg
        width="14"
        height="14"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
        class="shrink-0 transition-transform {{ $isRtl ? 'group-hover:-translate-x-0.5' : 'group-hover:translate-x-0.5' }}"
    >
        @if ($isRtl)
            {{-- Arabic: arrow points left --}}
            <path d="M19 12H5"/>
            <path d="m12 19-7-7 7-7"/>
        @else
            {{-- English: arrow points right --}}
            <path d="M5 12h14"/>
            <path d="m12 5 7 7-7 7"/>
        @endif
    </svg>
</a>