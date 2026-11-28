@extends('layouts.project')

@section('title', __('project.index_title'))
@section('back', route('learner.dashboard'))

@section('header_end')
    <a href="{{ route('projects.create') }}"
        class="rounded-xl bg-deep px-4 py-2 text-sm font-semibold text-white hover:bg-deep/90">
        {{ __('project.new_project') }}
    </a>
@endsection

@section('content')
    <div class="mx-auto max-w-5xl px-6 py-12">
        <h1 class="font-serif text-4xl text-ink">{{ __('project.index_title') }}</h1>
        <p class="mt-3 text-[15px] text-slate-500">{{ __('project.index_subtitle') }}</p>

        @if ($projects->isEmpty())
            <div class="mt-12 rounded-3xl border border-dashed border-slate-200 bg-white px-8 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-peach text-mauve">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                </div>
                <h2 class="mt-5 font-serif text-2xl text-ink">{{ __('project.empty_title') }}</h2>
                <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">{{ __('project.empty_text') }}</p>
                <a href="{{ route('projects.create') }}"
                    class="mt-6 inline-block rounded-xl bg-deep px-6 py-3 text-sm font-semibold text-white hover:bg-deep/90">
                    {{ __('project.empty_cta') }}
                </a>
            </div>
        @else
            <div class="mt-10 grid gap-6 md:grid-cols-2">
                @foreach ($projects as $project)
                    @php
                        $total = $project->stage_progress_count;
                        $done = $project->completed_stages_count;
                        $percent = $total > 0 ? (int) round(($done / $total) * 100) : 0;
                        $isLanding = $project->template->slug === 'landing-page';
                    @endphp

                    <div class="flex flex-col rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <span
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $isLanding ? 'bg-peach text-mauve' : 'bg-deep/10 text-deep' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="16 18 22 12 16 6" />
                                        <polyline points="8 6 2 12 8 18" />
                                    </svg>
                                </span>
                                <div>
                                    <h2 class="font-serif text-xl text-ink">{{ $project->title }}</h2>
                                    <p class="text-xs text-slate-400">
                                        {{ __('catalog.templates.' . $project->template->slug . '.name') }}
                                        ·
                                        {{ __('project.created_on', ['date' => $project->created_at->format('Y-m-d')]) }}
                                    </p>
                                </div>
                            </div>

                            @if ($project->status === 'completed')
                                <span
                                    class="rounded bg-emerald-50 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-600">{{ __('project.status_completed') }}</span>
                            @else
                                <span
                                    class="rounded bg-mauve/10 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-mauve">{{ __('project.status_in_progress') }}</span>
                            @endif
                        </div>

                        <div class="mt-6">
                            <div class="mb-2 flex justify-between text-xs text-slate-400">
                                <span>{{ __('project.progress', ['done' => $done, 'total' => $total]) }}</span>
                                <span>{{ $percent }}%</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-deep" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <a href="{{ route('projects.show', $project) }}"
                                class="inline-flex items-center gap-2 text-sm font-semibold text-deep hover:underline">
                                {{ __('project.continue') }}
                                <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg>
                            </a>

                            <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                onsubmit="return confirm(@js(__('project.confirm_delete')))">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="text-xs text-slate-400 hover:text-red-600">{{ __('project.delete') }}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
