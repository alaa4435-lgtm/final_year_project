@props([
    'icon' => null,
    'disabled' => false,
])

<div class="input-field rounded-lg flex items-center px-3 py-2.5 gap-2 bg-white">
    
    @if ($icon === 'email')
        <svg
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="#9CA3AF"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
        >
            <path d="m22 6-10 7L2 6"/>
            <rect x="2" y="4" width="20" height="16" rx="2"/>
        </svg>

    @elseif ($icon === 'password')
        <svg
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="#9CA3AF"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
        >
            <rect x="3" y="11" width="18" height="11" rx="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>

    @elseif ($icon === 'user')
        <svg
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="#9CA3AF"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
        >
            <circle cx="12" cy="7" r="4"/>
            <path d="M5.5 21a6.5 6.5 0 0 1 13 0"/>
        </svg>
    @endif

    <input
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full text-sm outline-none placeholder:text-gray-400 bg-transparent border-0 focus:ring-0'
        ]) }}
    >
</div>