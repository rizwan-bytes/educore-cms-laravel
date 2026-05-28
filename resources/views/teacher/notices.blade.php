@extends('layouts.app')
@section('title', __('notices.title'))

@section('content')

<div class="glass-card">
    <div class="card-header">
        <span>
            <i class="fas fa-bullhorn me-2" style="color:var(--orange)"></i>
            {{ __('notices.title') }}
            <span style="margin-left:8px;background:rgba(249,115,22,.15);color:var(--orange);padding:2px 10px;border-radius:20px;font-size:.75rem">
                {{ $notices->total() }} {{ __('notices.title') }}
            </span>
        </span>
    </div>

    <div style="padding:8px 0">
        @forelse($notices as $notice)
        <div class="notice-item">
            <div class="notice-icon-wrap">
                <i class="fas fa-bullhorn" style="color:var(--orange)"></i>
            </div>
            <div class="notice-body">
                <div class="notice-title">{{ $notice->title }}</div>
                <div class="notice-content">{{ $notice->content }}</div>
                <div class="notice-meta">
                    <span><i class="fas fa-user me-1"></i>{{ $notice->author->name ?? __('common.admin') }}</span>
                    <span style="margin:0 6px">·</span>
                    <span><i class="fas fa-calendar me-1"></i>{{ $notice->created_at->format('d M Y') }}</span>
                    @php
                        $badge = match($notice->target_role) {
                            'all'     => ['Everyone',  'rgba(99,102,241,.15)',  'var(--primary-lt)'],
                            'teacher' => ['Teachers',  'rgba(6,182,212,.12)',   'var(--cyan)'],
                            'student' => ['Students',  'rgba(16,185,129,.12)',  'var(--green)'],
                            'admin'   => ['Admin',     'rgba(249,115,22,.12)',  'var(--orange)'],
                            default   => [$notice->target_role, 'var(--surface)', 'var(--text-2)'],
                        };
                    @endphp
                    <span style="margin-left:8px;background:{{ $badge[1] }};color:{{ $badge[2] }};padding:1px 8px;border-radius:20px;font-size:.7rem">
                        {{ $badge[0] }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:60px;color:var(--muted)">
            <i class="fas fa-bullhorn fa-3x mb-3" style="display:block;opacity:.2"></i>
            {{ __('notices.no_notices') }}
        </div>
        @endforelse
    </div>

    @if($notices->hasPages())
    <div style="padding:12px 20px;border-top:1px solid var(--border)">
        {{ $notices->links() }}
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
.notice-item { display:flex; gap:14px; padding:16px 20px; border-bottom:1px solid var(--border); transition:background .15s; }
.notice-item:last-child { border-bottom:none; }
.notice-item:hover { background:rgba(255,255,255,.018); }
.notice-icon-wrap { width:40px; height:40px; border-radius:10px; background:rgba(249,115,22,.1); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.notice-body { flex:1; min-width:0; }
.notice-title { font-weight:600; color:var(--text); font-size:.9rem; margin-bottom:4px; }
.notice-content { color:var(--text-2); font-size:.84rem; line-height:1.55; margin-bottom:6px; }
.notice-meta { font-size:.74rem; color:var(--muted); display:flex; align-items:center; flex-wrap:wrap; }
</style>
@endpush
