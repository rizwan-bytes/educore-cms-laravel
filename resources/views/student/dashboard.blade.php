@extends('layouts.app')
@section('title', __('dashboard.title'))

@section('content')

@if(!$student)
<div class="glass-card text-center py-5">
    <i class="fas fa-triangle-exclamation fa-3x mb-3" style="color:var(--yellow);"></i>
    <h5 style="color:var(--text)">Student profile not linked.</h5>
    <p style="color:var(--text-2)">Please contact the administrator.</p>
</div>
@else

{{-- Student Info Banner --}}
<div class="glass-card mb-3" style="padding:20px 24px">
    <div class="d-flex align-items-center gap-4 flex-wrap">
        <div style="width:64px;height:64px;border-radius:50%;flex-shrink:0;
            background:{{ $student->gender === 'Female' ? 'linear-gradient(135deg,#ec4899,#db2777)' : 'linear-gradient(135deg,#6366f1,#8b5cf6)' }};
            display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:700;color:#fff">
            {{ substr($user->name, 0, 1) }}
        </div>
        <div style="flex:1">
            <h5 style="color:var(--text);margin:0;font-weight:600">{{ $user->name }}</h5>
            <div style="color:var(--text-2);font-size:.84rem;margin-top:4px">
                <span style="background:rgba(99,102,241,.12);color:var(--primary-lt);padding:2px 10px;border-radius:20px;font-size:.75rem;margin-right:8px">
                    {{ $class->name ?? '—' }}{{ ($class->section ?? '') ? ' — '.$class->section : '' }}
                </span>
                <span style="color:var(--muted)">
                    <i class="fas fa-id-card me-1"></i>{{ $student->roll_no ?? '—' }}
                </span>
            </div>
        </div>
        <div style="text-align:right">
            <div style="font-size:2rem;font-weight:700;color:{{ $attPct >= 75 ? 'var(--green)' : ($attPct >= 60 ? 'var(--yellow)' : 'var(--red)') }}">
                {{ $attPct }}%
            </div>
            <div style="font-size:.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:.04em">
                {{ __('attendance.percentage') }}
            </div>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,.12)">
                <i class="fas fa-calendar-days" style="color:var(--primary)"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalDays }}</div>
                <div class="stat-label">{{ __('attendance.total_days') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.12)">
                <i class="fas fa-circle-check" style="color:var(--green)"></i>
            </div>
            <div>
                <div class="stat-value">{{ $presentDays }}</div>
                <div class="stat-label">{{ __('attendance.present_days') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(239,68,68,.12)">
                <i class="fas fa-circle-xmark" style="color:var(--red)"></i>
            </div>
            <div>
                <div class="stat-value">{{ $absentDays }}</div>
                <div class="stat-label">{{ __('attendance.absent_days') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,158,11,.12)">
                <i class="fas fa-clock" style="color:var(--yellow)"></i>
            </div>
            <div>
                <div class="stat-value">{{ $lateDays }}</div>
                <div class="stat-label">{{ __('attendance.late_days') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Charts + Monthly --}}
<div class="row g-3 mb-3">
    {{-- This month --}}
    <div class="col-md-4">
        <div class="glass-card h-100">
            <div class="card-header">
                <span><i class="fas fa-calendar-check me-2" style="color:var(--green)"></i>{{ now()->format('F Y') }}</span>
            </div>
            <div style="padding:20px;text-align:center">
                <div style="font-size:3rem;font-weight:800;color:{{ $monthPct >= 75 ? 'var(--green)' : ($monthPct >= 60 ? 'var(--yellow)' : 'var(--red)') }}">
                    {{ $monthPct }}%
                </div>
                <div style="color:var(--muted);font-size:.8rem;margin-top:4px">
                    {{ $monthPresent }} / {{ $monthTotal }} {{ __('attendance.present_days') }}
                </div>
                <div style="margin-top:12px;background:var(--surface);border-radius:8px;height:8px;overflow:hidden">
                    <div style="height:100%;width:{{ $monthPct }}%;background:{{ $monthPct >= 75 ? 'var(--green)' : ($monthPct >= 60 ? 'var(--yellow)' : 'var(--red)') }};border-radius:8px"></div>
                </div>
                @if($monthPct < 75)
                <div style="margin-top:10px;font-size:.75rem;color:var(--yellow)">
                    <i class="fas fa-triangle-exclamation me-1"></i> Below 75% threshold
                </div>
                @endif
            </div>
        </div>
    </div>
    {{-- Last 7 days bar chart --}}
    <div class="col-md-8">
        <div class="glass-card h-100">
            <div class="card-header">
                <span><i class="fas fa-chart-bar me-2" style="color:var(--primary)"></i>{{ __('dashboard.last_7_days') }}</span>
                <a href="{{ route('student.attendance.index') }}" style="font-size:.8rem;color:var(--primary-lt)">{{ __('common.view_all') }} →</a>
            </div>
            <div style="padding:16px 20px">
                <canvas id="weekChart" height="110"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Recent Attendance + Notices --}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="glass-card">
            <div class="card-header">
                <span><i class="fas fa-list-check me-2" style="color:var(--cyan)"></i>{{ __('attendance.history') }}</span>
                <a href="{{ route('student.attendance.index') }}" style="font-size:.8rem;color:var(--primary-lt)">{{ __('common.view_all') }} →</a>
            </div>
            @forelse($recentAtt as $a)
            @php
                $sColor = ['Present'=>'#10b981','Absent'=>'#ef4444','Late'=>'#f59e0b'][$a->status] ?? 'var(--text-2)';
                $sBg    = ['Present'=>'rgba(16,185,129,.1)','Absent'=>'rgba(239,68,68,.1)','Late'=>'rgba(245,158,11,.1)'][$a->status] ?? 'var(--surface)';
            @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 20px;border-bottom:1px solid var(--border)">
                <div>
                    <div style="font-weight:500;color:var(--text);font-size:.875rem">{{ $a->date->format('d M Y') }}</div>
                    <div style="font-size:.73rem;color:var(--muted)">{{ $a->date->format('l') }}</div>
                </div>
                <span style="background:{{ $sBg }};color:{{ $sColor }};padding:3px 12px;border-radius:20px;font-size:.72rem;font-weight:500">
                    {{ __('attendance.'.strtolower($a->status)) }}
                </span>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:var(--muted);font-size:.875rem">{{ __('attendance.no_attendance') }}</div>
            @endforelse
        </div>
    </div>

    <div class="col-md-6">
        <div class="glass-card">
            <div class="card-header">
                <span><i class="fas fa-bullhorn me-2" style="color:var(--orange)"></i>{{ __('notices.title') }}</span>
                <a href="{{ route('student.notices.index') }}" style="font-size:.8rem;color:var(--primary-lt)">{{ __('common.view_all') }} →</a>
            </div>
            @forelse($notices as $n)
            <div style="display:flex;gap:12px;padding:12px 20px;border-bottom:1px solid var(--border);align-items:flex-start">
                <div style="width:34px;height:34px;border-radius:8px;background:rgba(249,115,22,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-bullhorn" style="color:var(--orange);font-size:.75rem"></i>
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-weight:500;color:var(--text);font-size:.875rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $n->title }}</div>
                    <div style="font-size:.73rem;color:var(--muted);margin-top:2px">{{ $n->created_at->format('d M Y') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:var(--muted);font-size:.875rem">{{ __('notices.no_notices') }}</div>
            @endforelse
        </div>
    </div>
</div>

@endif
@endsection

@push('scripts')
<script>
(function () {
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.font.size = 11;

    var labels = {!! json_encode($chartLabels ?? []) !!};
    var data   = {!! json_encode($chartData   ?? []) !!};

    var bgColors = data.map(function(v) {
        if (v === null) return 'rgba(75,85,99,.3)';
        if (v === 1)    return 'rgba(16,185,129,.7)';
        if (v === 0.5)  return 'rgba(245,158,11,.7)';
        return 'rgba(239,68,68,.7)';
    });

    new Chart(document.getElementById('weekChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: data.map(function(v){ return v === null ? 0 : 1; }),
                backgroundColor: bgColors,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            var v = data[ctx.dataIndex];
                            if (v === null) return 'No Record';
                            if (v === 1)    return '{{ __("attendance.present") }}';
                            if (v === 0.5)  return '{{ __("attendance.late") }}';
                            return '{{ __("attendance.absent") }}';
                        }
                    }
                }
            },
            scales: {
                y: { display: false, max: 1.3 },
                x: { grid: { color: 'rgba(255,255,255,.04)' } }
            }
        }
    });
})();
</script>
@endpush

@push('styles')
<style>
.stat-card { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:16px; display:flex; align-items:center; gap:14px; }
.stat-icon { width:46px; height:46px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.stat-value { font-size:1.5rem; font-weight:700; color:var(--text); line-height:1.1; }
.stat-label { font-size:.72rem; color:var(--muted); margin-top:2px; text-transform:uppercase; letter-spacing:.03em; }
</style>
@endpush
