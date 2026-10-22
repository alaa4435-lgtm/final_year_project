<div class="mt-6">
    <label class="flex items-start gap-2 text-xs text-gray-500">
        <input
            type="checkbox"
            name="terms"
            required
            class="mt-0.5 h-4 w-4 shrink-0 rounded border-gray-300"
            style="accent-color: var(--deep);"
        >


    <div class="leading-relaxed">
        {{ __('auth.terms_agreement') }}

        <a
            href="{{ url('/terms') }}"
            class="text-brand font-medium hover:underline"
        >
            {{ __('auth.terms_of_service') }}
        </a>

        {{ __('auth.and') }}

        <a
            href="{{ url('/privacy') }}"
            class="text-brand font-medium hover:underline"
        >
            {{ __('auth.privacy_policy') }}
        </a>.
    </div>
</label>

@error('terms')
    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
@enderror


</div>
