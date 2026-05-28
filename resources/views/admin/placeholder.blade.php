@extends('layouts.app')
@section('title', 'Coming Soon')
@section('content')
<div class="glass-card text-center py-5">
    <i class="fas fa-tools mb-3" style="font-size:3rem;color:var(--primary);opacity:.5;"></i>
    <h4 style="color:var(--text);">Module Coming in Phase 2</h4>
    <p style="color:var(--text-2);">This module will be built in the next phase.</p>
    <a href="{{ url()->previous() }}" class="btn-outline-custom">
        <i class="fas fa-arrow-left me-2"></i>{{ __('common.back') }}
    </a>
</div>
@endsection
