<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

        @if (app()->getLocale() === 'ar')

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
                radial-gradient(120% 120% at 0% 0%,
                    var(--cardBg) 0%,
                    var(--darkBg) 60%);
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
    </style>

</head>


<body class="antialiased">

    <div class="min-h-screen flex flex-col">

        <div class="flex-1 grid lg:grid-cols-2">
            <div class="hidden lg:flex lg:sticky lg:top-0 lg:h-screen lg:self-start panel-dark flex-col px-12 py-7 overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between" style="color: #F8F4E9">
                <x-application-logo />
            </div>


            {{-- Copy + Code Window --}}

            <div class="max-w-md mt-10">

                <p class="text-xs font-semibold tracking-wider uppercase" style="color: var(--peach);">
                    {{ __('messages.learn_build') }}
                </p>


                <h1 class="mt-3 text-3xl font-extrabold text-white leading-tight">

                    {{ __('messages.welcome') }}

                </h1>


                <p class="mt-3 text-sm text-gray-400 leading-relaxed">

                    {{ __('messages.platform_description', [
                        'default' =>
                            'Build your skills step by step with practical lessons, hands-on coding, and guidance whenever you need it.',
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
                    {{ app()->getLocale() === 'ar' ? '-translate-x-20' : 'translate-x-20' }}
                ">

                    <x-code-workspace />

                </div>

            </div>

        </div>



        {{-- ══════════════════════════════════════════════
        RIGHT: Authentication Content
    ══════════════════════════════════════════════ --}}

        <div class="relative flex flex-col px-6 sm:px-12 lg:px-20 py-10 bg-white">

            {{-- Top Bar --}}

            <div class="flex items-center justify-end gap-3">
                <x-language-switcher />

            </div>



            {{-- Authentication Content --}}

            <div class="flex-1 pt-16">

                <div class="w-full max-w-sm mx-auto">


                    {{-- Mobile Logo --}}

                    <a href="{{ url('/') }}"
                        class="lg:hidden flex items-center gap-2 text-brand font-extrabold text-lg mb-10">

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
    @if (request()->routeIs('terms', 'privacy'))
            <x-footer />
    @endif
    </div>
    

</body>


</html>
