@extends('legal.layout')

@section('title', __('privacy.privacy_title'))
@section('updated', '2026-11-14')

@section('content')
    @foreach (__('privacy.privacy_sections') as $section)
        <section>
            <h2 class="text-xl font-semibold mb-3">{{ $section['heading'] }}</h2>
            @foreach ($section['paragraphs'] as $p)
                <p class="mb-3">{{ $p }}</p>
            @endforeach
        </section>
    @endforeach
@endsection