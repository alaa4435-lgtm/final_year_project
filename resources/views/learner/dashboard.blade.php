@extends('layouts.project')

@section('title', __('learner_dashboard.title'))

@section('content')
<div class="mx-auto max-w-5xl px-6 py-12">
    <h1 class="font-serif text-4xl text-ink">
        {{ __('profile.greeting', ['name' => Str::before($user->name, ' ')]) }}
    </h1>
    <p class="mt-3 text-[15px] text-slate-500">{{ __('profile.subtitle') }}</p>

    {{-- Stat cards --}}
    <div class="mt-10 grid gap-6 md:grid-cols-3">
        @foreach ([
            ['label' => __('learner_dashboard.stats.total'), 'value' => $stats['total']],
            ['label' => __('learner_dashboard.stats.in_progress'), 'value' => $stats['in_progress']],
            ['label' => __('learner_dashboard.stats.completed'), 'value' => $stats['completed']],
        ] as $card)
            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="text-[11px] font-semibold uppercase tracking-[0.15em] text-slate-400">{{ $card['label'] }}</div>
                <div class="mt-3 font-serif text-4xl text-deep">{{ $card['value'] }}</div>
            </div>
        @endforeach
    </div>

    @if ($stats['total'] === 0)
        <div class="mt-12 rounded-3xl border border-dashed border-slate-200 bg-white px-8 py-16 text-center">
            <h2 class="font-serif text-2xl text-ink">{{ __('profile.empty_title') }}</h2>
            <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">{{ __('profile.empty_text') }}</p>
            <a href="{{ route('projects.create') }}"
               class="mt-6 inline-block rounded-xl bg-deep px-6 py-3 text-sm font-semibold text-white hover:bg-deep/90">
                {{ __('profile.empty_cta') }}
            </a>
        </div>
    @else
        {{-- Continue where you left off --}}
        @if ($current)
            @php
                $total = $current->stage_progress_count;
                $done = $current->completed_stages_count;
                $percent = $total > 0 ? (int) round($done / $total * 100) : 0;
            @endphp
            <div class="mt-12 rounded-3xl border border-slate-100 bg-white p-8 shadow-lg">
                <div class="text-[11px] font-semibold uppercase tracking-[0.15em] text-mauve">{{ __('profile.continue_title') }}</div>
                <h2 class="mt-2 font-serif text-2xl text-ink">{{ $current->title }}</h2>
                <p class="text-sm text-slate-400">{{ __('catalog.templates.' . $current->template->slug . '.name') }}</p>

                <div class="mt-6">
                    <div class="mb-2 flex justify-between text-xs text-slate-400">
                        <span>{{ __('project.progress', ['done' => $done, 'total' => $total]) }}</span>
                        <span>{{ $percent }}%</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-deep" style="width: {{ $percent }}%"></div>
                    </div>
                </div>

                <a href="{{ route('projects.show', $current) }}"
                   class="mt-6 inline-flex items-center gap-2 rounded-xl bg-deep px-6 py-3 text-sm font-semibold text-white hover:bg-deep/90">
                    {{ __('profile.continue_cta') }}
                    <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        @endif

        {{-- Recent projects --}}
        <div class="mt-12 flex items-center justify-between">
            <h2 class="font-serif text-2xl text-ink">{{ __('profile.recent_title') }}</h2>
            <a href="{{ route('projects.index') }}" class="text-sm font-semibold text-deep hover:underline">{{ __('profile.view_all') }}</a>
        </div>

        <div class="mt-5 space-y-3">
            @foreach ($recent as $project)
                <a href="{{ route('projects.show', $project) }}"
                   class="flex items-center justify-between rounded-2xl border border-slate-100 bg-white px-6 py-4 hover:shadow-sm">
                    <div>
                        <div class="font-semibold text-ink">{{ $project->title }}</div>
                        <div class="text-xs text-slate-400">
                            {{ __('catalog.templates.' . $project->template->slug . '.name') }}
                            · {{ __('project.progress', ['done' => $project->completed_stages_count, 'total' => $project->stage_progress_count]) }}
                        </div>
                    </div>
                    @if ($project->status === 'completed')
                        <span class="rounded bg-emerald-50 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-600">{{ __('project.status_completed') }}</span>
                    @else
                        <span class="rounded bg-mauve/10 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-mauve">{{ __('project.status_in_progress') }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection