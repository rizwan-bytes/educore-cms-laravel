@extends('layouts.app')
@section('title', __('dashboard.title'))

@section('content')

{{-- Welcome Row --}}
<div style="margin-bottom:20px">
    <h5 style="color:var(--text);font-weight:600;margin:0">
        {{ __('common.welcome') }}, {{ auth()->user()->name }}! 👋
    </h5>
    <p style="color:var(--text-2);font-size:.875rem;margin:4px 0 0">
        <i class="fas fa-calendar-day me-1" style="color:var(--cyan)"></i>
        {{ now()->format('l, d F Y') }}
    </p>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    {{-- Total Students --}}
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,.12)">
                <i class="fas fa-users" style="color:var(--primary)"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalStudents }}</div>
                <div class="stat-label">{{ __('dashboard.total_students') }}</div>
            </div>
        </div>
    </div>
    {{-- Total Classes --}}
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.12)">
                <i class="fas fa-school" style="color:var(--green)"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalClasses }}</div>
                <div class="stat-label">{{ __('dashboard.total_classes') }}</div>
            </div>
        </div>
    </div>
    {{-- Today Attendance % --}}
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(6,182,212,.12)">
                <i class="fas fa-calendar-check" style="color:var(--cyan)"></i>
            </div>
            <div>
                <div class="stat-value">
                    @if($todayAttPct !== null)
                        {{ $todayAttPct }}<span style="font-size:.9rem">%</span>
                    @else
                        <span style="font-size:1rem;color:var(--muted)">—</span>
                    @endif
                </div>
                <div class="stat-label">{{ __('dashboard.today_attendance') }}</div>
            </div>
        </div>
    </div>
    {{-- Notices --}}
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(249,115,22,.12)">
                <i class="fas fa-bullhorn" style="color:var(--orange)"></i>
            </div>
            <div>
                <div class="stat-value">{{ $noticesCount }}</div>
                <div class="stat-label">{{ __('notices.title') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Charts + Notices row --}}
<div class="row g-3 mb-4">
    {{-- Attendance Trend Chart --}}
    <div class="col-md-7">
        <div class="glass-card h-100">
            <div class="card-header">
                <span>
                    <i class="fas fa-chart-line me-2" style="color:var(--cyan)"></i>
                    {{ __('dashboard.attendance_trend') }}
                    <small style="color:var(--muted);font-size:.75rem;margin-left:6px">{{ __('dashboard.last_7_days') }}</small>
                </span>
            </div>
            <div style="padding:16px 20px">
                @if(array_sum($trendData) > 0)
                    <canvas id="trendChart" height="120"></canvas>
                @else
                    <div style="text-align:center;padding:40px;color:var(--muted)">
                        <i class="fas fa-chart-line fa-2x mb-2" style="display:block;opacity:.3"></i>
                        {{ __('dashboard.no_data') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Today's Breakdown --}}
    <div class="col-md-5">
        <div class="glass-card h-100">
            <div class="card-header">
                <span>
                    <i class="fas fa-chart-pie me-2" style="color:var(--primary)"></i>
                    {{ __('dashboard.today_breakdown') }}
                </span>
            </div>
            <div style="padding:16px 20px">
                @if($todayTotal > 0)
                    <canvas id="todayChart" height="130"></canvas>
                    <div class="d-flex justify-content-center gap-3 mt-3" style="font-size:.78rem">
                        <span><span style="color:#10b981">●</span> {{ __('attendance.present') }} ({{ $todayPresent }})</span>
                        <span><span style="color:#ef4444">●</span> {{ __('attendance.absent') }} ({{ $todayAbsent }})</span>
                        <span><span style="color:#f59e0b">●</span> {{ __('attendance.late') }} ({{ $todayLate }})</span>
                    </div>
                @else
                    <div style="text-align:center;padding:40px;color:var(--muted)">
                        <i class="fas fa-calendar-xmark fa-2x mb-2" style="display:block;opacity:.3"></i>
                        {{ __('dashboard.not_marked_today') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions + Notices --}}
<div class="row g-3">
    {{-- Quick Actions --}}
    <div class="col-md-4">
        <div class="glass-card">
            <div class="card-header">
                <span><i class="fas fa-bolt me-2" style="color:var(--yellow)"></i>{{ __('dashboard.quick_actions') }}</span>
            </div>
            <div style="padding:16px">
                <a href="{{ route('teacher.attendance.index') }}" class="qa-link">
                    <i class="fas fa-calendar-check" style="color:var(--green)"></i>
                    <span>{{ __('attendance.mark') }}</span>
                    <i class="fas fa-chevron-right ms-auto" style="color:var(--muted);font-size:.7rem"></i>
                </a>
                <a href="{{ route('teacher.notices.index') }}" class="qa-link">
                    <i class="fas fa-bullhorn" style="color:var(--orange)"></i>
                    <span>{{ __('notices.title') }}</span>
                    <i class="fas fa-chevron-right ms-auto" style="color:var(--muted);font-size:.7rem"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Notices --}}
    <div class="col-md-8">
        <div class="glass-card">
            <div class="card-header">
                <span><i class="fas fa-bullhorn me-2" style="color:var(--orange)"></i>{{ __('notices.title') }}</span>
                <a href="{{ route('teacher.notices.index') }}" style="font-size:.8rem;color:var(--primary-lt)">
                    {{ __('common.view_all') }} →
                </a>
            </div>
            <div style="padding:8px 16px">
                @forelse($recentNotices as $n)
                <div style="padding:12px 8px;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:flex-start">
                    <div style="width:36px;height:36px;border-radius:8px;background:rgba(249,115,22,.12);
                                display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-bullhorn" style="color:var(--orange);font-size:.8rem"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:500;color:var(--text);font-size:.875rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $n->title }}
                        </div>
                        <div style="font-size:.75rem;color:var(--muted);margin-top:2px">
                            {{ $n->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:30px;color:var(--muted);font-size:.875rem">
                    {{ __('notices.no_notices') }}
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.font.size = 11;

    @if(array_sum($trendData) > 0)
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($trendLabels) !!},
            datasets: [{
                label: '{{ __("attendance.percentage") }}',
                data: {!! json_encode($trendData) !!},
                borderColor: 'rgba(6,182,212,0.9)',
                backgroundColor: 'rgba(6,182,212,0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(6,182,212,0.9)',
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    min: 0, max: 100,
                    ticks: { callback: v => v + '%' },
                    grid: { color: 'rgba(255,255,255,.04)' }
                },
                x: { grid: { color: 'rgba(255,255,255,.04)' } }
            }
        }
    });
    @endif

    @if($todayTotal > 0)
    new Chart(document.getElementById('todayChart'), {
        type: 'doughnut',
        data: {
            labels: ['{{ __("attendance.present") }}', '{{ __("attendance.absent") }}', '{{ __("attendance.late") }}'],
            datasets: [{
                data: [{{ $todayPresent }}, {{ $todayAbsent }}, {{ $todayLate }}],
                backgroundColor: ['rgba(16,185,129,0.75)', 'rgba(239,68,68,0.75)', 'rgba(245,158,11,0.75)'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });
    @endif
})();
</script>
@endpush

@push('styles')
<style>
.stat-card { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:16px; display:flex; align-items:center; gap:14px; }
.stat-icon { width:46px; height:46px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.stat-value { font-size:1.5rem; font-weight:700; color:var(--text); line-height:1.1; }
.stat-label { font-size:.72rem; color:var(--muted); margin-top:2px; text-transform:uppercase; letter-spacing:.03em; }
.qa-link {
    display:flex; align-items:center; gap:12px;
    padding:11px 12px; border-radius:9px; text-decoration:none;
    color:var(--text-2); font-size:.875rem; transition:background .15s;
    margin-bottom:4px;
}
.qa-link:hover { background:var(--surface); color:var(--text); }
</style>
@endpush
