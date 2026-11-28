@extends('layouts.project')

@section('title', $project->title)
@section('back', route('projects.index'))

@php
    $done = $stages->where('status', 'completed')->count();
    $total = $stages->count();
    $templateName = __('catalog.templates.' . $project->template->slug . '.name');
@endphp

@section('header_end')
    <div class="text-end leading-tight">
        <div class="text-[10px] font-semibold uppercase tracking-[0.15em] text-slate-400">{{ __('project.active_project') }}</div>
        <div class="text-sm font-semibold text-ink">{{ $project->title }}</div>
    </div>
    <div class="font-serif text-lg font-semibold text-deep">{{ $done }}/{{ $total }}</div>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-6 py-12">
    <h1 class="font-serif text-4xl text-ink">{{ __('project.roadmap_title') }}</h1>
    <p class="mt-3 text-[15px] text-slate-500">
        {{ __('project.roadmap_subtitle', ['template' => $templateName]) }}
    </p>

    <div class="relative mt-12">
        {{-- الخط العمودي --}}
        <div class="absolute bottom-6 top-6 w-0.5 bg-slate-200 start-5"></div>

        @foreach ($stages as $progress)
            @php
                $stage = $progress->stage;
                $titleKey = 'catalog.stages.' . $stage->slug;
                $descKey = 'catalog.stage_descriptions.' . $stage->slug;
                $stageTitle = Lang::has($titleKey) ? __($titleKey) : $stage->title;
                $stageDesc = Lang::has($descKey) ? __($descKey) : $stage->description;
                $guideUrl = Route::has('stage-guide.show') ? route('stage-guide.show', [$project, $stage]) : null;
            @endphp

            <div class="relative pb-10 ps-16">
                @if ($progress->status === 'completed')
                    <span class="absolute start-0 top-5 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500 text-white shadow">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <div class="rounded-2xl border border-slate-100 bg-white/60 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <h2 class="font-serif text-lg text-slate-500">{{ $stage->order }}. {{ $stageTitle }}</h2>
                            <span class="rounded bg-emerald-50 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-600">{{ __('project.stage_completed') }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-400">{{ $stageDesc }}</p>
                    </div>

                @elseif ($progress->status === 'in_progress')
                    <span class="absolute start-[-4px] top-4 flex h-12 w-12 items-center justify-center rounded-full bg-deep text-sm font-semibold text-white ring-8 ring-mauve/25">
                        {{ $stage->order }}
                    </span>
                    <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-lg">
                        <div class="flex items-start justify-between gap-4">
                            <h2 class="font-serif text-2xl text-ink">{{ $stageTitle }}</h2>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-deep">{{ __('project.stage_current') }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-500">{{ $stageDesc }}</p>

                        <div class="mt-6 flex items-center gap-4">
                            @if ($guideUrl)
                                <a href="{{ $guideUrl }}" class="inline-flex items-center gap-2 rounded-xl bg-deep px-6 py-3 text-sm font-semibold text-white hover:bg-deep/90">
                                    {{ __('project.enter_guide') }}
                                    <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            @else
                                <span title="{{ __('project.guide_unavailable') }}" class="inline-flex cursor-not-allowed items-center gap-2 rounded-xl bg-deep/50 px-6 py-3 text-sm font-semibold text-white">
                                    {{ __('project.enter_guide') }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1 text-xs text-slate-400">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ __('project.time_estimate', ['minutes' => 45]) }}
                            </span>
                        </div>
                    </div>

                @else
                    <span class="absolute start-0 top-5 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17 8h-1V6a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2zm-7-2a2 2 0 0 1 4 0v2h-4V6z"/></svg>
                    </span>
                    <div class="rounded-2xl border border-slate-100 bg-white/40 p-5 opacity-60">
                        <h2 class="font-serif text-lg text-slate-500">{{ $stage->order }}. {{ $stageTitle }}</h2>
                        <p class="mt-1 text-sm text-slate-400">{{ $stageDesc }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection