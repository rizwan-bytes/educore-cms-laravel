@extends('layouts.app')
@section('title', __('attendance.title'))

@section('content')

@if(!$student)
<div class="glass-card text-center py-5">
    <i class="fas fa-triangle-exclamation fa-3x mb-3" style="color:var(--yellow);"></i>
    <h5 style="color:var(--text)">Student profile not linked.</h5>
</div>
@else

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,.12)">
                <i class="fas fa-calendar-days" style="color:var(--primary)"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
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
                <div class="stat-value">{{ $stats['present'] }}</div>
                <div class="stat-label">{{ __('attendance.present') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(239,68,68,.12)">
                <i class="fas fa-circle-xmark" style="color:var(--red)"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['absent'] }}</div>
                <div class="stat-label">{{ __('attendance.absent') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left:3px solid {{ $stats['pct'] >= 75 ? 'var(--green)' : ($stats['pct'] >= 60 ? 'var(--yellow)' : 'var(--red)') }}">
            <div class="stat-icon" style="background:rgba(99,102,241,.12)">
                <i class="fas fa-percent" style="color:var(--primary)"></i>
            </div>
            <div>
                <div class="stat-value" style="color:{{ $stats['pct'] >= 75 ? 'var(--green)' : ($stats['pct'] >= 60 ? 'var(--yellow)' : 'var(--red)') }}">
                    {{ $stats['pct'] }}%
                </div>
                <div class="stat-label">{{ __('attendance.percentage') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Monthly Chart --}}
<div class="glass-card mb-4">
    <div class="card-header">
        <span><i class="fas fa-chart-bar me-2" style="color:var(--primary)"></i>Monthly Breakdown</span>
    </div>
    <div style="padding:16px 20px">
        <canvas id="monthlyChart" height="90"></canvas>
    </div>
</div>

{{-- Records Table --}}
<div class="glass-card">
    <div class="card-header">
        <span>
            <i class="fas fa-calendar-check me-2" style="color:var(--green)"></i>
            {{ __('attendance.history') }}
            <span class="badge-count" id="totalCount"
                  style="margin-left:8px;background:rgba(16,185,129,.15);color:var(--green);padding:2px 10px;border-radius:20px;font-size:.75rem"></span>
        </span>
        <div class="d-flex gap-2">
            {{-- Status Filter --}}
            <select id="statusFilter" class="form-select form-select-sm"
                    style="width:130px;background:var(--surface);border-color:var(--border);color:var(--text)">
                <option value="">{{ __('attendance.all_statuses') }}</option>
                <option value="Present">{{ __('attendance.present') }}</option>
                <option value="Absent">{{ __('attendance.absent') }}</option>
                <option value="Late">{{ __('attendance.late') }}</option>
            </select>
        </div>
    </div>

    <table id="attTable" class="table-dark-custom" style="width:100%">
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>{{ __('attendance.date') }}</th>
                <th>Day</th>
                <th>{{ __('attendance.status') }}</th>
            </tr>
        </thead>
    </table>
</div>

@endif
@endsection

@push('scripts')
<script>
@if($student)
// Monthly chart
(function () {
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.font.size = 11;

    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyLabels) !!},
            datasets: [
                {
                    label: '{{ __("attendance.present") }}',
                    data: {!! json_encode($monthlyPresent) !!},
                    backgroundColor: 'rgba(16,185,129,0.7)',
                    borderRadius: 4,
                },
                {
                    label: '{{ __("attendance.absent") }}',
                    data: {!! json_encode($monthlyAbsent) !!},
                    backgroundColor: 'rgba(239,68,68,0.6)',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top', labels: { boxWidth: 12, padding: 16 } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,.04)' }, ticks: { precision: 0 } },
                x: { grid: { color: 'rgba(255,255,255,.04)' } }
            }
        }
    });
})();

// DataTable
var attTable = $('#attTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("student.attendance.data") }}',
        type: 'GET',
        data: function (d) { d.status = $('#statusFilter').val(); }
    },
    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'date_fmt',    name: 'date' },
        { data: 'day_name',    name: 'day_name', orderable: false, searchable: false },
        { data: 'status_badge',name: 'status',   orderable: false, searchable: false },
    ],
    dom: "<'dt-toolbar'<'dt-left'f><'dt-right'l>><'dt-table't><'dt-footer'ip>",
    pageLength: 20,
    lengthMenu: [[10, 20, 50, -1], [10, 20, 50, 'All']],
    order: [[1, 'desc']],
    language: {
        search: '', searchPlaceholder: '{{ __("attendance.search") }}',
        processing: '<div class="dt-spinner-card"><i class="fas fa-spinner fa-spin"></i><span>{{ __("common.loading") }}</span></div>',
        emptyTable: '<div class="dt-empty">{{ __("attendance.no_attendance") }}</div>',
        zeroRecords: '<div class="dt-empty">{{ __("attendance.no_attendance") }}</div>',
        info: 'Showing _START_–_END_ of _TOTAL_',
        infoEmpty: 'Showing 0 of 0',
        paginate: {
            previous: '<i class="fas fa-chevron-left"></i>',
            next:     '<i class="fas fa-chevron-right"></i>',
        },
    },
    drawCallback: function (settings) {
        var info = this.api().page.info();
        $('#totalCount').text(info.recordsTotal + ' {{ __("attendance.records") }}');
    }
});

$('#statusFilter').on('change', function () { attTable.ajax.reload(); });
@endif
</script>
@endpush

@push('styles')
<style>
.stat-card { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:16px; display:flex; align-items:center; gap:14px; }
.stat-icon { width:46px; height:46px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.stat-value { font-size:1.5rem; font-weight:700; color:var(--text); line-height:1.1; }
.stat-label { font-size:.72rem; color:var(--muted); margin-top:2px; text-transform:uppercase; letter-spacing:.03em; }
.dt-toolbar .dataTables_filter input,
.dt-toolbar .dataTables_length select { height:34px !important; }
.dt-table { overflow-x:auto; }
</style>
@endpush
