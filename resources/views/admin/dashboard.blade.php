@extends('layouts.app')
@section('title', __('dashboard.title'))

@section('content')

{{-- ── Row 1: Main Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon gradient"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['students'] }}</div>
                <div class="stat-label">{{ __('dashboard.total_students') }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon cyan"><i class="fas fa-chalkboard-user"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['teachers'] }}</div>
                <div class="stat-label">{{ __('dashboard.total_teachers') }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-info">
                <div class="stat-value">₨{{ number_format($stats['fees_paid'] / 1000, 1) }}k</div>
                <div class="stat-label">{{ __('dashboard.fees_collected') }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-triangle-exclamation"></i></div>
            <div class="stat-info">
                <div class="stat-value">₨{{ number_format(($stats['fees_pending'] + $stats['fees_overdue']) / 1000, 1) }}k</div>
                <div class="stat-label">{{ __('dashboard.outstanding_fees') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Row 2: Secondary Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-layer-group"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['classes'] }}</div>
                <div class="stat-label">{{ __('classes.title') }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-book-open"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['subjects'] }}</div>
                <div class="stat-label">{{ __('subjects.title') }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon cyan"><i class="fas fa-bell"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['notices'] }}</div>
                <div class="stat-label">{{ __('dashboard.active_notices') }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon {{ $attOverall >= 75 ? 'green' : ($attOverall >= 50 ? 'yellow' : 'red') }}">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $attOverall }}%</div>
                <div class="stat-label">{{ __('dashboard.overall_attendance') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Row 3: Attendance Trend + Fee Doughnut ── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="glass-card h-100">
            <div class="card-header">
                <span><i class="fas fa-chart-column me-2" style="color:var(--primary-lt)"></i>{{ __('dashboard.attendance_trend') }}</span>
                <span style="font-size:.75rem;color:var(--muted)">{{ __('dashboard.last_7_days') }}</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:240px">
                    <canvas id="attChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="glass-card h-100">
            <div class="card-header">
                <span><i class="fas fa-circle-half-stroke me-2" style="color:var(--green)"></i>{{ __('dashboard.fee_status') }}</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center gap-3">
                <div class="chart-container" style="height:180px;width:180px">
                    <canvas id="feeChart"></canvas>
                </div>
                <div class="d-flex gap-3 flex-wrap justify-content-center">
                    <div class="chart-legend-item"><span class="chart-dot" style="background:var(--green)"></span>{{ __('fees.paid') }}</div>
                    <div class="chart-legend-item"><span class="chart-dot" style="background:var(--yellow)"></span>{{ __('fees.pending') }}</div>
                    <div class="chart-legend-item"><span class="chart-dot" style="background:var(--red)"></span>{{ __('fees.overdue') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Row 4: Monthly Fee Trend + Grade Distribution ── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="glass-card h-100">
            <div class="card-header">
                <span><i class="fas fa-chart-line me-2" style="color:var(--cyan)"></i>{{ __('dashboard.monthly_collection') }}</span>
                <span style="font-size:.75rem;color:var(--muted)">{{ __('dashboard.last_6_months') }}</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:220px">
                    <canvas id="feeMonthChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="glass-card h-100">
            <div class="card-header">
                <span><i class="fas fa-chart-bar me-2" style="color:var(--yellow)"></i>{{ __('dashboard.grade_distribution') }}</span>
                <span style="font-size:.75rem;color:var(--muted)">{{ __('dashboard.all_results') }}</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:220px">
                    <canvas id="gradeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Row 5: Students by Class + Notices ── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="glass-card h-100">
            <div class="card-header">
                <span><i class="fas fa-users me-2" style="color:var(--orange)"></i>{{ __('dashboard.students_by_class') }}</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:{{ max(160, count($classData) * 42) }}px">
                    <canvas id="classChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="glass-card h-100">
            <div class="card-header">
                <span><i class="fas fa-bell me-2" style="color:var(--yellow)"></i>{{ __('dashboard.latest_notices') }}</span>
                <a href="{{ route('admin.notices.index') }}" class="btn-primary-custom"
                   style="padding:.3rem .7rem;font-size:.75rem">{{ __('common.view_all') }}</a>
            </div>
            <div class="card-body">
                @forelse($notices as $notice)
                <div class="notice-card">
                    <h6>{{ $notice->title }}</h6>
                    <p>{{ \Illuminate\Support\Str::limit($notice->content, 90) }}</p>
                    <div class="notice-meta">
                        <span class="badge-status {{ $notice->target_role }}">{{ ucfirst($notice->target_role) }}</span>
                        · {{ $notice->created_at->format('d M Y') }}
                    </div>
                </div>
                @empty
                <p style="color:var(--muted);text-align:center;padding:2rem">{{ __('common.no_data') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ── Recent Students ── --}}
<div class="glass-card">
    <div class="card-header">
        <span><i class="fas fa-user-graduate me-2" style="color:var(--primary-lt)"></i>{{ __('dashboard.recent_students') }}</span>
        <a href="{{ route('admin.students.index') }}" class="btn-primary-custom"
           style="padding:.3rem .7rem;font-size:.75rem">{{ __('common.view_all') }}</a>
    </div>
    <div style="overflow-x:auto">
        <table class="table-dark-custom">
            <thead>
                <tr>
                    <th>{{ __('common.name') }}</th>
                    <th>{{ __('students.roll_no') }}</th>
                    <th>{{ __('classes.title') }}</th>
                    <th>{{ __('common.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentStudents as $s)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($s->user?->avatar)
                                <img src="{{ asset('storage/' . $s->user->avatar) }}" class="table-avatar-img" alt="">
                            @else
                                <div class="table-avatar">{{ strtoupper(substr($s->user?->name ?? '?', 0, 1)) }}</div>
                            @endif
                            <div>
                                <div style="font-weight:600">{{ $s->user?->name }}</div>
                                <div style="font-size:.75rem;color:var(--muted)">{{ $s->user?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--text-2)">{{ $s->roll_no ?? '—' }}</td>
                    <td>{{ $s->class?->name }}{{ $s->class?->section ? ' ' . $s->class->section : '' }}</td>
                    <td>
                        <span class="badge-status {{ $s->status ? 'active' : 'inactive' }}">
                            {{ $s->status ? __('common.active') : __('common.inactive') }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;color:var(--muted);padding:2rem">{{ __('common.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Global Chart Defaults ──────────────────────────────────────────
const C = Chart.defaults;
C.color         = '#94a3b8';
C.borderColor   = 'rgba(255,255,255,.05)';
C.font.family   = "'Inter', system-ui, sans-serif";
C.font.size     = 11;

const TOOLTIP = {
    backgroundColor: '#111827',
    borderColor:     'rgba(255,255,255,.1)',
    borderWidth: 1,
    titleColor:  '#f1f5f9',
    bodyColor:   '#94a3b8',
    padding: 10,
    cornerRadius: 8
};

const SCALE = (stacked = false) => ({
    x: { ticks:{ color:'#4b5563' }, grid:{ color:'rgba(255,255,255,.04)', drawBorder:false }, stacked },
    y: { ticks:{ color:'#4b5563' }, grid:{ color:'rgba(255,255,255,.04)', drawBorder:false }, stacked }
});

function emptyMsg(canvasId, msg) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx2 = canvas.getContext('2d');
    ctx2.fillStyle = '#4b5563';
    ctx2.font = "14px 'Inter', sans-serif";
    ctx2.textAlign = 'center';
    ctx2.fillText(msg || 'No data yet', canvas.width / 2, canvas.height / 2);
}

// ── Chart 1: Attendance 7-day ──────────────────────────────────────
(function() {
    const data = {!! json_encode($attData) !!};
    const el = document.getElementById('attChart');
    if (!el) return;
    if (!data.length) { emptyMsg('attChart', 'No attendance recorded yet'); return; }

    new Chart(el, {
        type: 'bar',
        data: {
            labels: data.map(d => {
                const dt = new Date(d.date + 'T00:00:00');
                return dt.toLocaleDateString('en-GB', { weekday:'short', day:'2-digit', month:'short' });
            }),
            datasets: [
                {
                    label: 'Present',
                    data: data.map(d => parseInt(d.present) || 0),
                    backgroundColor: 'rgba(16,185,129,.75)',
                    borderRadius: { topLeft:6, topRight:6 },
                    borderSkipped: false,
                },
                {
                    label: 'Absent',
                    data: data.map(d => parseInt(d.absent) || 0),
                    backgroundColor: 'rgba(239,68,68,.55)',
                    borderRadius: { topLeft:6, topRight:6 },
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend:{ labels:{ color:'#94a3b8', boxWidth:10, boxHeight:10 } }, tooltip: TOOLTIP },
            scales: SCALE(),
        }
    });
})();

// ── Chart 2: Fee Doughnut ──────────────────────────────────────────
(function() {
    const data = {!! json_encode(array_values($feeChart)) !!};
    const el = document.getElementById('feeChart');
    if (!el) return;
    const total = data.reduce((a,b) => a+b, 0);
    if (!total) { emptyMsg('feeChart', 'No fee data'); return; }

    new Chart(el, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Pending', 'Overdue'],
            datasets: [{
                data,
                backgroundColor: ['rgba(16,185,129,.8)', 'rgba(245,158,11,.8)', 'rgba(239,68,68,.8)'],
                borderColor:     ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 2,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...TOOLTIP,
                    callbacks: {
                        label: ctx => ` ₨${Number(ctx.parsed).toLocaleString()}`
                    }
                }
            }
        }
    });
})();

// ── Chart 3: Monthly Fee Collection Line ──────────────────────────
(function() {
    const data = {!! json_encode($monthlyFees) !!};
    const el = document.getElementById('feeMonthChart');
    if (!el) return;
    if (!data.length) { emptyMsg('feeMonthChart', 'No paid fees recorded yet'); return; }

    new Chart(el, {
        type: 'line',
        data: {
            labels: data.map(d => d.month),
            datasets: [{
                label: 'Collected (₨)',
                data:  data.map(d => parseFloat(d.total)),
                borderColor:     '#06b6d4',
                backgroundColor: 'rgba(6,182,212,.1)',
                borderWidth: 2.5,
                pointBackgroundColor: '#06b6d4',
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: TOOLTIP },
            scales: {
                x: { ticks:{ color:'#4b5563' }, grid:{ color:'rgba(255,255,255,.04)', drawBorder:false } },
                y: {
                    ticks:{
                        color:'#4b5563',
                        callback: v => '₨' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v)
                    },
                    grid:{ color:'rgba(255,255,255,.04)', drawBorder:false }
                }
            }
        }
    });
})();

// ── Chart 4: Grade Distribution ────────────────────────────────────
(function() {
    const data = {!! json_encode($gradeData) !!};
    const el = document.getElementById('gradeChart');
    if (!el) return;
    if (!data.length) { emptyMsg('gradeChart', 'No results entered yet'); return; }

    const gradeColors = {
        'A+':'rgba(16,185,129,.8)', 'A':'rgba(16,185,129,.6)',
        'B': 'rgba(6,182,212,.8)',
        'C': 'rgba(99,102,241,.8)',
        'D': 'rgba(245,158,11,.8)',
        'F': 'rgba(239,68,68,.8)'
    };

    new Chart(el, {
        type: 'bar',
        data: {
            labels: data.map(d => d.grade),
            datasets: [{
                label: 'Students',
                data:  data.map(d => parseInt(d.count)),
                backgroundColor: data.map(d => gradeColors[d.grade] || 'rgba(99,102,241,.6)'),
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend:{ display:false }, tooltip: TOOLTIP },
            scales: {
                x: { ticks:{ color:'#4b5563' }, grid:{ display:false } },
                y: { ticks:{ color:'#4b5563', stepSize:1 }, grid:{ color:'rgba(255,255,255,.04)', drawBorder:false } }
            }
        }
    });
})();

// ── Chart 5: Students per Class (horizontal bar) ──────────────────
(function() {
    const data = {!! json_encode($classData) !!};
    const el = document.getElementById('classChart');
    if (!el) return;
    if (!data.length) { emptyMsg('classChart', 'No classes yet'); return; }

    new Chart(el, {
        type: 'bar',
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                label: 'Students',
                data:  data.map(d => parseInt(d.count)),
                backgroundColor: 'rgba(99,102,241,.7)',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend:{ display:false }, tooltip: TOOLTIP },
            scales: {
                x: { ticks:{ color:'#4b5563', stepSize:1 }, grid:{ color:'rgba(255,255,255,.04)', drawBorder:false } },
                y: { ticks:{ color:'#94a3b8' }, grid:{ display:false } }
            }
        }
    });
})();
</script>
@endpush
