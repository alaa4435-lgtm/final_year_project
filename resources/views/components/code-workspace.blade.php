{{-- Right: code window mockup — Aurie's Stage 1 workspace, split code / live preview --}}

{{-- Window title bar --}}

<div class="titlebar flex items-center gap-2 px-5 py-3.5 border-b border-white/10">

<span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
<span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
<span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>

<h1 class="ms-3 text-xs sm:text-sm font-semibold text-white font-mono tracking-wide">
    Stage 1 · Navigation Bar
</h1>

</div>

<div class="grid grid-cols-2 min-h-[300px]">

{{-- Code half: HTML/CSS/JS phase tabs --}}
<div class="border-e border-white/10">

    <div class="px-4 pt-4 flex gap-1 text-xs font-mono">

        <span
            class="px-3 py-1.5 rounded-t-md font-semibold"
            style="background: var(--cardBg); color: var(--peach);"
        >
            HTML
        </span>

        <span class="px-3 py-1.5 rounded-t-md text-gray-500">
            CSS
        </span>

        <span class="px-3 py-1.5 rounded-t-md text-gray-500">
            JS
        </span>

    </div>


    <div
        class="px-5 py-6 text-xs sm:text-sm font-mono leading-7"
        style="background: var(--cardBg);"
    >

        <div>
            <span class="text-pink-300">&lt;nav&gt;</span>
        </div>

        <div class="ps-4">
            <span class="text-pink-300">&lt;a&gt;</span>
            <span class="text-gray-200">Home</span>
            <span class="text-pink-300">&lt;/a&gt;</span>
        </div>

        <div class="ps-4">
            <span class="text-pink-300">&lt;a&gt;</span>
            <span class="text-gray-200">About</span>
            <span class="text-pink-300">&lt;/a&gt;</span>
        </div>

        <div class="ps-4">
            <span class="text-pink-300">&lt;a&gt;</span>
            <span class="text-gray-200">Contact</span>
            <span class="text-pink-300">&lt;/a&gt;</span>
        </div>

        <div>
            <span class="text-pink-300">&lt;/nav&gt;</span>
        </div>

    </div>

</div>


{{-- Preview half --}}
<div>

    <div
        class="px-4 pt-4 text-[10px] font-mono uppercase tracking-[0.12em] text-gray-400"
    >
        Preview
    </div>

    <div
        class="h-[calc(100%-1.75rem)] px-5 py-6 flex items-start gap-5 text-sm font-medium bg-white"
    >

        <span
            class="transition-opacity hover:opacity-70"
            style="color: var(--deep);"
        >
            Home
        </span>

        <span
            class="transition-opacity hover:opacity-70"
            style="color: var(--deep);"
        >
            About
        </span>

        <span
            class="transition-opacity hover:opacity-70"
            style="color: var(--deep);"
        >
            Contact
        </span>

    </div>

</div>

</div>

{{-- AI assistant strip --}}

<div class="px-5 py-4 border-t border-white/10">

<div class="mt-3 flex items-center justify-end gap-2">

    <span
        class="text-[11px] font-medium px-3 py-1.5 rounded-lg text-gray-200 bg-white/10"
    >
        Explain
    </span>

    <span
        class="text-[11px] font-medium px-3 py-1.5 rounded-lg text-gray-200 bg-white/10"
    >
        Hint
    </span>

    <span
        class="text-[11px] font-medium px-3 py-1.5 rounded-lg shadow-sm"
        style="background: var(--mauve); color:#fff;"
    >
        Check My Work
    </span>

</div>

</div>
