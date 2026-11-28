@props(['user', 'size' => 'h-10 w-10', 'text' => 'text-sm'])

<span {{ $attributes->merge(['class' => "inline-flex {$size} shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#502D55] font-semibold text-white {$text}"]) }}>
    @if ($user->avatar)
        <img src="{{ $user->avatar_url }}" alt="" class="h-full w-full object-cover">
    @else
        {{ $user->initials }}
    @endif
</span>