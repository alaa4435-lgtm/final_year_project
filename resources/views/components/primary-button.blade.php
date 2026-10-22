@props([
    'type' => 'submit',
])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center px-4 py-2 bg-brand-deep border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand transition ease-in-out duration-150'
    ]) }}
>
    {{ $slot }}
</button>