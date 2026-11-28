
@extends('layouts.project')

@section('title', __('profile.title'))
@section('back', $user->dashboardUrl())

@section('content')
@php
    $input = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-ink focus:border-deep focus:outline-none focus:ring-2 focus:ring-deep/20';
    $label = 'mb-1.5 block text-sm font-medium text-ink';
    $card = 'rounded-3xl border border-slate-100 bg-white p-8 shadow-sm';
    $primary = 'rounded-xl bg-deep px-6 py-2.5 text-sm font-semibold text-white hover:bg-deep/90';
    $mustVerify = $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail();
@endphp

<div class="mx-auto max-w-3xl px-6 py-12">
    <div class="flex flex-wrap items-center gap-3">
        <h1 class="font-serif text-4xl text-ink">{{ __('profile.title') }}</h1>
    </div>

    <p class="mt-3 text-[15px] text-slate-500">{{ __('profile.settings_subtitle') }}</p>

    @if (session('status') && Lang::has('profile.flash.' . session('status')))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 3000)"
             x-show="show"
             x-transition.opacity.duration.500ms
             data-auto-dismiss="3000"
             class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ __('profile.flash.' . session('status')) }}
        </div>
    @endif

    {{-- Hidden forms --}}
    <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="hidden">
        @csrf
    </form>

    {{-- Account information --}}
    <section id="info-card" class="{{ $card }} mt-10">
        <h2 class="font-serif text-2xl text-ink">{{ __('profile.info_title') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('profile.info_hint') }}</p>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
            @csrf
            @method('PATCH')

            {{-- Avatar --}}
            <div>
                <div class="flex items-center gap-5">
                    <x-avatar :user="$user" size="h-20 w-20" text="text-2xl" id="avatar-preview" />

                    <div>
                        <label for="avatar" class="{{ $label }}">
                            {{ __('profile.avatar_label') }}
                        </label>

                        <div class="flex flex-wrap items-center gap-3">
                            <label for="avatar"
                                   class="cursor-pointer rounded-lg bg-deep px-4 py-2 text-sm font-semibold text-white hover:bg-mauve">
                                {{ __('profile.choose_file') }}
                            </label>

                            <span id="file-name" class="text-sm text-slate-500">
                                {{ __('profile.no_file') }}
                            </span>

                            <input
                                id="avatar"
                                name="avatar"
                                type="file"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                class="hidden"
                            >
                        </div>

                        <p class="mt-2 text-xs text-slate-400">
                            {{ __('profile.hint') }}
                        </p>
                    </div>
                </div>

                @if ($user->avatar)
                    <label class="mt-3 flex items-center gap-2 text-sm text-slate-500">
                        <input id="remove-avatar" type="checkbox" name="remove_avatar" value="1"
                               class="rounded border-slate-300 text-deep focus:ring-deep/30">
                        {{ __('profile.avatar_remove') }}
                    </label>
                @endif

                @error('avatar')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Name --}}
            <div>
                <label for="name" class="{{ $label }}">{{ __('profile.name_label') }}</label>
                <input id="name" name="name" type="text"
                       value="{{ old('name', $user->name) }}"
                       required autocomplete="name" class="{{ $input }}">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="{{ $label }}">{{ __('profile.email_label') }}</label>
                <input id="email" name="email" type="email"
                       value="{{ old('email', $user->email) }}"
                       required autocomplete="username" class="{{ $input }}">

                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror

                @if ($mustVerify)
                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        {{ __('profile.unverified_text') }}

                        <button type="submit" form="send-verification" class="font-semibold underline">
                            {{ __('profile.resend_link') }}
                        </button>
                    </div>
                @endif
            </div>

            <div class="flex justify-end">
                <button type="submit" class="{{ $primary }}">
                    {{ __('profile.save') }}
                </button>
            </div>
        </form>
    </section>

    {{-- Password --}}
    <section id="password-card" class="{{ $card }} mt-8">
        <h2 class="font-serif text-2xl text-ink">{{ __('profile.password_title') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('profile.password_hint') }}</p>

        <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="update_password_current_password" class="{{ $label }}">
                    {{ __('profile.current_password') }}
                </label>
                <input id="update_password_current_password" name="current_password"
                       type="password" autocomplete="current-password" class="{{ $input }}">

                @if ($errors->updatePassword->has('current_password'))
                    <p class="mt-1 text-xs text-red-600">
                        {{ $errors->updatePassword->first('current_password') }}
                    </p>
                @endif
            </div>

            <div>
                <label for="update_password_password" class="{{ $label }}">
                    {{ __('profile.new_password') }}
                </label>
                <input id="update_password_password" name="password"
                       type="password" autocomplete="new-password" class="{{ $input }}">

                @if ($errors->updatePassword->has('password'))
                    <p class="mt-1 text-xs text-red-600">
                        {{ $errors->updatePassword->first('password') }}
                    </p>
                @endif
            </div>

            <div>
                <label for="update_password_password_confirmation" class="{{ $label }}">
                    {{ __('profile.confirm_password') }}
                </label>
                <input id="update_password_password_confirmation" name="password_confirmation"
                       type="password" autocomplete="new-password" class="{{ $input }}">

                @if ($errors->updatePassword->has('password_confirmation'))
                    <p class="mt-1 text-xs text-red-600">
                        {{ $errors->updatePassword->first('password_confirmation') }}
                    </p>
                @endif
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <a href="{{ route('password.request') }}"
                   class="text-sm font-medium text-deep underline hover:text-mauve">
                    {{ __('auth.forgot_password') }}
                </a>

                <button type="submit" class="{{ $primary }}">
                    {{ __('profile.update_password') }}
                </button>
            </div>
        </form>
    </section>

    {{-- Delete account --}}
    <section class="mt-8 rounded-3xl border border-red-100 bg-white p-8 shadow-sm">
        <h2 class="font-serif text-2xl text-red-700">{{ __('profile.delete_title') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('profile.delete_text') }}</p>

        <button type="button" id="open-delete"
                class="mt-6 rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
            {{ __('profile.delete_button') }}
        </button>
    </section>
</div>

{{-- Delete confirmation --}}
<dialog id="delete-dialog" class="w-full max-w-md rounded-3xl p-0 shadow-xl backdrop:bg-slate-900/40">
    <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-5 p-8 text-start">
        @csrf
        @method('DELETE')

        <div>
            <h2 class="font-serif text-2xl text-ink">{{ __('profile.delete_modal_title') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ __('profile.delete_modal_text') }}</p>
        </div>

        <div>
            <label for="delete_password" class="{{ $label }}">
                {{ __('profile.current_password') }}
            </label>
            <input id="delete_password" name="password" type="password"
                   autocomplete="current-password" class="{{ $input }}">

            @if ($errors->userDeletion->has('password'))
                <p class="mt-1 text-xs text-red-600">
                    {{ $errors->userDeletion->first('password') }}
                </p>
            @endif
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <button type="button" id="cancel-delete"
                    class="rounded-xl px-5 py-2.5 text-sm text-slate-500 hover:text-slate-700">
                {{ __('profile.cancel') }}
            </button>

            <button type="submit"
                    class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                {{ __('profile.delete_confirm') }}
            </button>
        </div>
    </form>
</dialog>

<script>
    (function () {
        // Avatar preview and file name
        const input = document.getElementById('avatar');
        const box = document.getElementById('avatar-preview');
        const fileName = document.getElementById('file-name');
        const removeBox = document.getElementById('remove-avatar');

        input.addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (!file) {
                return;
            }

            // Display selected file name
            fileName.textContent = file.name;

            // Preview selected avatar
            const img = document.createElement('img');
            img.className = 'h-full w-full object-cover';
            img.alt = '';
            img.src = URL.createObjectURL(file);

            box.replaceChildren(img);

            if (removeBox) {
                removeBox.checked = false;
            }
        });

        // Delete account confirmation
        const dialog = document.getElementById('delete-dialog');

        document.getElementById('open-delete').addEventListener('click', function () {
            dialog.showModal();
        });

        document.getElementById('cancel-delete').addEventListener('click', function () {
            dialog.close();
        });

        @if ($errors->userDeletion->isNotEmpty())
            dialog.showModal();
        @endif

        @if ($errors->updatePassword->isNotEmpty())
            document.getElementById('password-card').scrollIntoView({ block: 'center' });
        @endif
    })();
</script>
@endsection
