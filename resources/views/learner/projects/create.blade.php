@extends('layouts.project')

@section('title', __('project.create_title'))
@section('back', route('projects.index'))

@section('header_end')
    <span id="step-label"
          data-step1="{{ __('project.step', ['current' => 1, 'total' => 2]) }}"
          data-step2="{{ __('project.step', ['current' => 2, 'total' => 2]) }}"
          class="text-[11px] font-semibold uppercase tracking-[0.15em] text-slate-400">
        {{ __('project.step', ['current' => 1, 'total' => 2]) }}
    </span>
@endsection

@section('content')
<div class="mx-auto max-w-4xl px-6 py-16 text-center">
    <h1 class="font-serif text-4xl text-ink md:text-5xl">{{ __('project.create_heading') }}</h1>
    <p class="mx-auto mt-5 max-w-md text-[15px] leading-relaxed text-slate-500">{{ __('project.create_subtitle') }}</p>

    <div class="mx-auto mt-14 grid max-w-[820px] gap-8 text-start md:grid-cols-2">
        @foreach ($templates as $template)
            @php
                $tplName = __('catalog.templates.' . $template->slug . '.name');
            @endphp
            <div class="flex flex-col rounded-3xl border border-slate-100 bg-white p-8 shadow-sm">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl {{ $template->slug === 'landing-page' ? 'bg-peach text-mauve' : 'bg-deep/10 text-deep' }}">
                    @if ($template->slug === 'landing-page')
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/>
                            <path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/>
                            <path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>
                        </svg>
                    @else
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/>
                        </svg>
                    @endif
                </div>

                <h2 class="mt-7 font-serif text-2xl text-ink">{{ $tplName }}</h2>
                <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-500">
                    {{ __('catalog.templates.' . $template->slug . '.description') }}
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        {{ trans_choice('project.stages_count', $template->stages_count) }}
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        {{ __('catalog.templates.' . $template->slug . '.level') }}
                    </span>
                </div>

                <button type="button"
                        class="select-btn mt-7 self-start text-sm font-semibold {{ $template->slug === 'landing-page' ? 'text-mauve' : 'text-deep' }} hover:underline"
                        data-template-id="{{ $template->id }}"
                        data-template-name="{{ $tplName }}"
                        data-default-title="{{ __('project.default_title', ['template' => $tplName]) }}">
                    {{ __('project.select_project') }}
                </button>
            </div>
        @endforeach
    </div>

    <p class="mt-16 text-xs italic text-slate-400">
        “{{ __('project.quote') }}” — {{ __('project.quote_author') }}
    </p>
</div>

{{-- Step 2: name the project --}}
<dialog id="name-dialog" class="w-full max-w-md rounded-3xl p-0 shadow-xl backdrop:bg-slate-900/40">
    <form method="POST" action="{{ route('projects.store') }}" class="space-y-5 p-8 text-start">
        @csrf
        <input type="hidden" name="template_id" id="template-id" value="{{ old('template_id') }}">

        <div>
            <h2 class="font-serif text-2xl text-ink">{{ __('project.name_title') }}</h2>
            <p class="mt-1 text-sm text-slate-500">
                {{ __('project.name_hint') }} <span id="chosen-template" class="font-semibold text-deep"></span>
            </p>
        </div>

        <div>
            <label for="title-input" class="mb-1 block text-sm font-medium text-ink">{{ __('project.name_label') }}</label>
            <input id="title-input" name="title" type="text" maxlength="255" value="{{ old('title') }}"
                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-deep focus:outline-none focus:ring-2 focus:ring-deep/20">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            @error('template_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <button type="button" id="cancel-btn" class="rounded-xl px-5 py-2.5 text-sm text-slate-500 hover:text-slate-700">
                {{ __('project.cancel') }}
            </button>
            <button type="submit" class="rounded-xl bg-deep px-5 py-2.5 text-sm font-semibold text-white hover:bg-deep/90">
                {{ __('project.create_project') }}
            </button>
        </div>
    </form>
</dialog>

<script>
    (function () {
        const dialog = document.getElementById('name-dialog');
        const templateInput = document.getElementById('template-id');
        const titleInput = document.getElementById('title-input');
        const chosen = document.getElementById('chosen-template');
        const stepLabel = document.getElementById('step-label');

        function setStep(n) { stepLabel.textContent = stepLabel.dataset['step' + n]; }

        document.querySelectorAll('.select-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                templateInput.value = btn.dataset.templateId;
                chosen.textContent = btn.dataset.templateName;
                if (!titleInput.value) { titleInput.value = btn.dataset.defaultTitle; }
                setStep(2);
                dialog.showModal();
                titleInput.focus();
                titleInput.select();
            });
        });

        document.getElementById('cancel-btn').addEventListener('click', function () { dialog.close(); });
        dialog.addEventListener('close', function () { setStep(1); });

        // إعادة فتح النافذة إذا فشل التحقق في الخادم
        @if ($errors->has('title') || $errors->has('template_id'))
            const preset = @json(old('template_id'));
            const btn = document.querySelector('[data-template-id="' + preset + '"]');
            if (btn) { btn.click(); }
        @endif
    })();
</script>
@endsection