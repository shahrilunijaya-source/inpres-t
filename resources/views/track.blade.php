@extends('layouts.portal')

@section('title', 'Status Permohonan ' . $application->reference_number . ' — Portal InPreS')

@section('main')
{{-- ============ Page header (pine) ============ --}}
<section class="pine-hero" id="main" style="padding:64px 0 56px;">
    <div class="container">
        <div style="margin-bottom:20px;">
            <a href="{{ route('track.search') }}" class="text-sm inline-flex items-center gap-1 hover:underline" style="color:var(--teal-700);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                Semak rujukan lain
            </a>
        </div>
        <span class="eyebrow on-pine">
            Semak Status
            <span class="e-dot orange"></span>
        </span>
        <h1 style="margin:14px 0 0;">Status Permohonan</h1>
    </div>
</section>

{{-- ============ Body ============ --}}
<section class="section">
    <div class="container">
        <x-smart-tracker :application="$application" :prediction="$prediction" />
    </div>
</section>
@endsection
