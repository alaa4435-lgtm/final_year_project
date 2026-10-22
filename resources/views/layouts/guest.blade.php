<!DOCTYPE html>

<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ─────────────────────────────────────────────
           Violet Dusk Palette
        ───────────────────────────────────────────── */

        :root {
            --deep: #502D55;
            --mauve: #935073;
            --peach: #F6DBC0;
            --ivory: #F8F4E9;
            --darkBg: #2A1630;
            --cardBg: #3D2143;
            --border: #6B3F6E;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: #2B1730;
        }

        /* ─────────────────────────────────────────────
           RTL
        ───────────────────────────────────────────── */

        @if(app()->getLocale() === 'ar')

            body {
                direction: rtl;
                text-align: right;
            }

            .ltr {
                direction: ltr !important;
                text-align: left !important;
            }

        @endif

        /* ─────────────────────────────────────────────
           Brand
        ───────────────────────────────────────────── */

        .text-brand {
            color: var(--deep);
        }

        .bg-brand-deep {
            background: var(--deep);
        }

        /* ─────────────────────────────────────────────
           Primary Button
        ───────────────────────────────────────────── */

        .btn-primary {
            background: var(--deep);
            color: var(--ivory);

            transition:
                background .15s ease,
                transform .15s ease;
        }

        .btn-primary:hover {
            background: #3f2244;
            transform: translateY(-1px);
        }

        /* ─────────────────────────────────────────────
           Dark Panel
        ───────────────────────────────────────────── */

        .panel-dark {
            background:
                radial-gradient(
                    120% 120% at 0% 0%,
                    var(--cardBg) 0%,
                    var(--darkBg) 60%
                );
        }

        /* ─────────────────────────────────────────────
           Code Window
        ───────────────────────────────────────────── */

        .code-window {
            background: var(--darkBg);
            border-radius: 14px;

            /* Code editor must always stay LTR */
            direction: ltr !important;
            text-align: left !important;
        }

        .titlebar span {
            width: 9px;
            height: 9px;
            border-radius: 9999px;
            display: inline-block;
        }

        /* ─────────────────────────────────────────────
           Input
        ───────────────────────────────────────────── */

        .input-field {
            border: 1px solid #E5E1DC;

            transition:
                border-color .15s ease,
                box-shadow .15s ease;
        }

        .input-field:focus-within {
            border-color: var(--mauve);

            box-shadow:
                0 0 0 3px rgba(147, 80, 115, 0.15);
        }

        /* ─────────────────────────────────────────────
           Language Switcher
        ───────────────────────────────────────────── */

        .language-switcher {
            display: flex;
            align-items: center;
            gap: 8px;

            padding: 6px 12px;

            background: rgba(255, 255, 255, 0.1);

            border: 1px solid rgba(147, 80, 115, 0.3);

            border-radius: 8px;
        }

        .language-switcher select {
            background: transparent;
            border: none;

            font-size: 13px;
            font-weight: 500;

            color: var(--deep);

            cursor: pointer;

            padding: 0;
            outline: none;
        }

        .language-switcher select:hover {
            color: var(--mauve);
        }
    </style>

</head>


<body class="antialiased">

<div class="min-h-screen grid lg:grid-cols-2">


    {{-- ══════════════════════════════════════════════
        LEFT: Brand / Product Panel
    ══════════════════════════════════════════════ --}}

    <div
        class="hidden lg:flex panel-dark flex-col px-12 py-7 relative overflow-hidden"
    >

        {{-- Header --}}
        <div
            class="flex items-center justify-between"
            style="color: #F8F4E9"
        >
            <x-application-logo />
        </div>


        {{-- Copy + Code Window --}}

        <div class="max-w-md mt-10">

            <p
                class="text-xs font-semibold tracking-wider uppercase"
                style="color: var(--peach);"
            >
                {{ __('messages.learn_build') }}
            </p>


            <h1 class="mt-3 text-3xl font-extrabold text-white leading-tight">

                {{ __('messages.welcome') }}

            </h1>


            <p class="mt-3 text-sm text-gray-400 leading-relaxed">

                {{ __('messages.platform_description', [
                    'default' => 'Build your skills step by step with practical lessons, hands-on coding, and guidance whenever you need it.'
                ]) }}

            </p>


            {{-- Code Window --}}

            <div
                class="
                    code-window
                    overflow-hidden
                    mt-8
                    border
                    border-white/10
                    ltr
                    {{ app()->getLocale() === 'ar'
                        ? '-translate-x-20'
                        : 'translate-x-20'
                    }}
                "
            >

                <x-code-workspace />

            </div>

        </div>

    </div>



    {{-- ══════════════════════════════════════════════
        RIGHT: Authentication Content
    ══════════════════════════════════════════════ --}}

    <div
        class="relative flex flex-col px-6 sm:px-12 lg:px-20 py-10 bg-white"
    >

        {{-- Top Bar --}}

        <div class="flex items-center justify-end gap-3">


            {{-- Language Switcher --}}

            <div class="language-switcher">

                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="text-gray-500 shrink-0"
                >
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M2 12h20"/>
                    <path d="M12 2a15.3 15.3 0 0 1 0 20"/>
                    <path d="M12 2a15.3 15.3 0 0 0 0 20"/>
                </svg>


                <select
                    aria-label="{{ __('auth.language') }}"
                    onchange="window.location.href = this.value"
                >

                    <option
                        value="{{ route('language.switch', ['locale' => 'en']) }}"
                        {{ app()->getLocale() === 'en' ? 'selected' : '' }}
                    >
                        {{ __('auth.english') }}
                    </option>


                    <option
                        value="{{ route('language.switch', ['locale' => 'ar']) }}"
                        {{ app()->getLocale() === 'ar' ? 'selected' : '' }}
                    >
                        {{ __('auth.arabic') }}
                    </option>

                </select>

            </div>



            {{-- Back to Home --}}

            <a
                href="{{ url('/') }}"
                class="btn-primary inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold"
            >

                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    @if(app()->getLocale() === 'ar')

                        {{-- Arrow pointing right --}}

                        <path d="M5 12h14"/>
                        <path d="m12 5 7 7-7 7"/>

                    @else

                        {{-- Arrow pointing left --}}

                        <path d="M19 12H5"/>
                        <path d="m12 19-7-7 7-7"/>

                    @endif

                </svg>


                {{ __('auth.back_to_home') }}

            </a>

        </div>



        {{-- Authentication Content --}}

        <div class="flex-1 pt-16">

            <div class="w-full max-w-sm mx-auto">


                {{-- Mobile Logo --}}

                <a
                    href="{{ url('/') }}"
                    class="lg:hidden flex items-center gap-2 text-brand font-extrabold text-lg mb-10"
                >

                    <span aria-hidden="true">
                        ♦
                    </span>

                    {{ config('app.name', 'Laravel') }}

                </a>


                {{-- Auth Page Content --}}

                {{ $slot }}

            </div>

        </div>

    </div>

</div>

</body>
</html>