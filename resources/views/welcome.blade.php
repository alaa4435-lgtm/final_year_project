<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurie — Learn Front-End Development by Building</title>

    {{-- Tailwind (utility classes) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Aurie "Violet Dusk" palette ── */
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

        .btn-primary {
            background: var(--deep);
            color: var(--ivory);
            transition: background .15s ease, transform .15s ease;
        }

        .btn-primary:hover {
            background: #3f2244;
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            color: var(--deep);
            border: 1px solid var(--border);
        }

        .btn-outline:hover {
            background: var(--ivory);
        }

        .btn-ghost {
            color: var(--deep);
        }

        .btn-ghost:hover {
            color: var(--mauve);
        }

        .text-brand {
            color: var(--deep);
        }

        .bg-brand-deep {
            background: var(--deep);
        }

        .bg-brand-ivory {
            background: var(--ivory);
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--peach);
            color: var(--deep);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .code-window {
            background: var(--darkBg);
            border-radius: 14px;
            box-shadow: 0 25px 50px -12px rgba(42, 22, 48, 0.45);
        }

        .code-window .titlebar span {
            width: 10px;
            height: 10px;
            border-radius: 9999px;
            display: inline-block;
        }

        .step-circle {
            width: 48px;
            height: 48px;
            border-radius: 9999px;
            background: var(--deep);
            color: var(--ivory);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }
    </style>
</head>

<body class="bg-white antialiased">

    {{-- ══════════════ NAVBAR ══════════════ --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 h-18 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-brand font-extrabold text-xl">
                <span aria-hidden="true">♦</span> Aurie
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="#features" class="hover:text-brand transition">Features</a>
                <a href="#how-it-works" class="hover:text-brand transition">How It Works</a>
                <a href="#about" class="hover:text-brand transition">About</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="btn-ghost text-sm font-semibold px-3 py-2">Log In</a>
                <a href="{{ route('register') }}" class="btn-primary text-sm font-semibold px-5 py-2.5 rounded-lg">Get
                    Started</a>
            </div>
        </div>
    </header>

    {{-- ══════════════ HERO ══════════════ --}}
    <section class="bg-brand-ivory relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 lg:py-24 grid lg:grid-cols-2 gap-14 lg:gap-20 items-center">

            <div class="max-w-xl">
                <h1
                    class="text-4xl sm:text-5xl lg:text-[3.5rem] font-extrabold leading-[1.1] tracking-tight text-gray-900">
                    Learn to code <span class="text-brand">by actually building</span>
                </h1>

                <p class="mt-6 text-lg text-gray-600 max-w-lg leading-8">
                    Aurie teaches HTML, CSS, and JavaScript one stage at a time.
                    Read a short lesson, write real code, and see your own website take shape.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('register') }}" class="btn-primary px-7 py-3.5 rounded-xl font-semibold text-sm
                              shadow-lg shadow-purple-900/10
                              transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl">
                        Get Started
                    </a>

                    <a href="#how-it-works" class="btn-outline px-7 py-3.5 rounded-xl font-semibold text-sm
                              transition-all duration-200 hover:-translate-y-0.5">
                        How It Works
                    </a>
                </div>
            </div>

            {{-- Right: code window mockup — Aurie's Stage 1 workspace, split code / live preview --}}
            <div id="demo" class="code-window overflow-hidden rounded-2xl shadow-2xl shadow-purple-900/10
            border border-black/4">
            <x-code-workspace />
            </div>
        </div>
    </section>

    {{-- ══════════════ FEATURES ══════════════ --}}
    <section id="features" class="max-w-7xl mx-auto px-6 lg:px-10 py-16 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">What's inside</h2>
        <p class="mt-3 text-gray-500 max-w-xl mx-auto">Everything you need to go from a blank page to a finished
            website.</p>

        <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
            @foreach ([
                    [
                        'title' => 'Stage Guides',
                        'text' => 'A short lesson before every stage — just the concept you need, nothing extra.',
                        'icon' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>',
                    ],
                    [
                        'title' => 'Live Workspace',
                        'text' => 'Write HTML, CSS, and JavaScript with a preview that updates as you type.',
                        'icon' => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
                    ],
                    [
                        'title' => 'AI Assistant',
                        'text' => 'Ask for an explanation, a hint, or a check on your work — never the full answer.',
                        'icon' => '<rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8" y2="16"/><line x1="16" y1="16" x2="16" y2="16"/>',
                    ],
                    [
                        'title' => 'Two Templates',
                        'text' => 'Build a Portfolio or a Landing Page, section by section — Navbar to Footer.',
                        'icon' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
                    ],
                    [
                        'title' => 'Save & Resume',
                        'text' => 'Your progress saves automatically, so you can pick up right where you left off.',
                        'icon' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
                    ],
                    [
                        'title' => 'Deploy Your Site',
                        'text' => 'Finish every stage, then see your project live as a real deployed website.',
                        'icon' => '<path d="M12 19V5"/><path d="m5 12 7-7 7 7"/>',
                    ],
                ] as $f)
                <div class="rounded-2xl border border-gray-100 p-6 hover:shadow-lg hover:-translate-y-0.5 transition">
                    <div class="feature-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">{!! $f['icon'] !!}</svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900">{{ $f['title'] }}</h3>
                    <p class="mt-1.5 text-sm text-gray-500 leading-relaxed">{{ $f['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ══════════════ STATS BAR ══════════════ --}}
    <section class="bg-brand-deep">
    <div class="max-w-4xl mx-auto px-6 lg:px-10 py-10 grid grid-cols-1 sm:grid-cols-3 gap-8 text-center text-white">
        @foreach ([
            ['value' => '2', 'label' => 'Project templates'],
            ['value' => '3', 'label' => 'AI assistant modes'],
            ['value' => '6', 'label' => 'Stages per project'],
        ] as $s)
            <div>
                <div class="text-3xl sm:text-4xl font-extrabold">
                    {{ $s['value'] }}
                </div>

                <div class="mt-1 text-sm text-white/70">
                    {{ $s['label'] }}
                </div>
            </div>
        @endforeach
    </div>
</section>

    {{-- ══════════════ LEARNING PATHWAY ══════════════ --}}
    <section id="how-it-works" class="bg-brand-ivory">
        <div class="max-w-5xl mx-auto px-6 lg:px-10 py-16 text-center">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">How it works</h2>
            <p class="mt-3 text-gray-500">Three steps, from first tag to finished site.</p>

            <div class="mt-12 grid md:grid-cols-3 gap-10">
                @foreach ([
                        ['num' => '1', 'title' => 'Pick a project', 'text' => 'Choose Portfolio or Landing Page. Aurie sets up the stages for you.'],
                        ['num' => '2', 'title' => 'Learn, then build', 'text' => 'Read the guide, then write the code yourself with AI support close by.'],
                        ['num' => '3', 'title' => 'Ship it', 'text' => 'Pass each stage, then deploy your finished project as a live site.'],
                    ] as $step)
                    <div class="flex flex-col items-center">
                        <div class="step-circle">{{ $step['num'] }}</div>
                        <h3 class="mt-4 font-semibold text-gray-900">{{ $step['title'] }}</h3>
                        <p class="mt-1.5 text-sm text-gray-500 leading-relaxed max-w-xs">{{ $step['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════════ CTA ══════════════ --}}
    <section id="about" class="bg-brand-deep">
        <div class="max-w-2xl mx-auto px-6 lg:px-10 py-16 text-center text-white">
            <h2 class="text-3xl sm:text-4xl font-extrabold">Ready to build something?</h2>
            <p class="mt-3 text-white/70">Start free. No experience, no credit card.</p>
            <a href="{{ route('register') }}"
                class="inline-block mt-7 bg-white text-brand font-semibold px-7 py-3.5 rounded-lg hover:bg-gray-100 transition">
                Create Free Account
            </a>
        </div>
    </section>

    {{-- ══════════════ FOOTER ══════════════ --}}
    <x-footer />

</body>

</html>