@extends('layouts.app')
@section('title', __('attendance.title'))

@section('content')

{{-- Page Header --}}
<div class="glass-card">
    <div class="card-header">
        <span>
            <i class="fas fa-calendar-check me-2" style="color:var(--green)"></i>
            {{ __('attendance.history') }}
            <span class="badge-count" id="totalCount"
                  style="margin-left:8px;background:rgba(16,185,129,.15);color:var(--green);padding:2px 10px;border-radius:20px;font-size:.75rem"></span>
        </span>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            {{-- Class Filter --}}
            <select id="classFilter" class="form-select form-select-sm"
                    style="width:170px;background:var(--surface);border-color:var(--border);color:var(--text)">
                <option value="">{{ __('attendance.all_classes') }}</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">
                        {{ $class->name }}{{ $class->section ? ' — '.$class->section : '' }}
                    </option>
                @endforeach
            </select>
            {{-- Status Filter --}}
            <select id="statusFilter" class="form-select form-select-sm"
                    style="width:130px;background:var(--surface);border-color:var(--border);color:var(--text)">
                <option value="">{{ __('attendance.all_statuses') }}</option>
                <option value="Present">{{ __('attendance.present') }}</option>
                <option value="Absent">{{ __('attendance.absent') }}</option>
                <option value="Late">{{ __('attendance.late') }}</option>
            </select>
            {{-- Date Filter --}}
            <input type="date" id="dateFilter" class="form-control form-control-sm"
                   style="width:155px;background:var(--surface);border-color:var(--border);color:var(--text)">
            {{-- Mark Button --}}
            <a href="{{ route('admin.attendance.mark') }}" class="btn-primary-custom">
                <i class="fas fa-pen-to-square"></i> {{ __('attendance.mark') }}
            </a>
        </div>
    </div>

    <table id="attendanceTable" class="table-dark-custom" style="width:100%">
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>{{ __('attendance.student_name') }}</th>
                <th>{{ __('attendance.class') }}</th>
                <th>{{ __('attendance.date') }}</th>
                <th>{{ __('attendance.status') }}</th>
            </tr>
        </thead>
    </table>
</div>

@endsection

@push('scripts')
<script>
// ──────────────────────────────────────────────────────────────────────────
// ATTENDANCE HISTORY — DataTable
// ──────────────────────────────────────────────────────────────────────────

var attendanceTable;

$(document).ready(function () {
    attendanceTable = $('#attendanceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.attendance.data") }}',
            type: 'GET',
            data: function (d) {
                d.class_id = $('#classFilter').val();
                d.status   = $('#statusFilter').val();
                d.date     = $('#dateFilter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex',   name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'student_name',  name: 'student_name', orderable: false, searchable: false },
            { data: 'class_name',    name: 'class_name',   orderable: false, searchable: false },
            { data: 'date_fmt',      name: 'date' },
            { data: 'status_badge',  name: 'status',       orderable: false, searchable: false },
        ],
        dom: "<'dt-toolbar'<'dt-left'f><'dt-right'Bl>><'dt-table't><'dt-footer'ip>",
        buttons: [
            {
                extend: 'csv',
                className: 'btn-dt-export',
                text: '<i class="fas fa-file-csv me-1"></i>CSV',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'print',
                className: 'btn-dt-export',
                text: '<i class="fas fa-print me-1"></i>{{ __("common.print") }}',
                exportOptions: { columns: ':not(:last-child)' },
                customize: function(win) {
                    var style = win.document.createElement('style');
                    style.innerHTML = [
                        'body { font-family:Arial,sans-serif; background:#fff; color:#111; padding:20px; margin:0; }',
                        'h1   { font-size:16px; font-weight:700; color:#111; margin:0 0 2px; }',
                        '.print-meta { font-size:11px; color:#6b7280; margin-bottom:16px; border-bottom:2px solid #e5e7eb; padding-bottom:10px; }',
                        'table { border-collapse:collapse; width:100%; }',
                        'thead th { background:#f3f4f6 !important; color:#374151 !important; font-weight:700;',
                        '           font-size:10px; text-transform:uppercase; padding:9px 10px; border:1px solid #e5e7eb; }',
                        'tbody td { padding:8px 10px; border:1px solid #e5e7eb; color:#111; font-size:12px; background:#fff; }',
                        'tbody tr:nth-child(even) td { background:#f9fafb; }',
                        'tfoot { display:none; }',
                        '@page { margin:15mm; }'
                    ].join(' ');
                    win.document.head.appendChild(style);
                    var h1 = win.document.querySelector('h1');
                    if (h1) {
                        h1.outerHTML = '<h1>{{ \App\Services\SettingService::get("college_name", "EduCore CMS") }}</h1>' +
                                       '<div class="print-meta">{{ __("attendance.history") }} &nbsp;|&nbsp; ' +
                                       'Printed: ' + new Date().toLocaleDateString("en-PK",{day:"2-digit",month:"short",year:"numeric"}) + '</div>';
                    }
                }
            },
        ],
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']],
        order: [[3, 'desc']],
        language: {
            search:           '',
            searchPlaceholder:'{{ __("attendance.search") }}',
            processing:       '<div class="dt-spinner-card"><i class="fas fa-spinner fa-spin"></i><span>{{ __("common.loading") }}</span></div>',
            emptyTable:       '<div class="dt-empty">{{ __("attendance.no_attendance") }}</div>',
            zeroRecords:      '<div class="dt-empty">{{ __("attendance.no_attendance") }}</div>',
            info:             'Showing _START_–_END_ of _TOTAL_',
            infoEmpty:        'Showing 0 of 0',
            infoFiltered:     '(filtered from _MAX_)',
            paginate: {
                first:    '<i class="fas fa-angles-left"></i>',
                last:     '<i class="fas fa-angles-right"></i>',
                previous: '<i class="fas fa-chevron-left"></i>',
                next:     '<i class="fas fa-chevron-right"></i>',
            },
        },
        drawCallback: function (settings) {
            var info = this.api().page.info();
            $('#totalCount').text(info.recordsTotal + ' {{ __("attendance.records") }}');
        }
    });

    // Reload on filter change
    $('#classFilter, #statusFilter').on('change', function () {
        attendanceTable.ajax.reload();
    });
    $('#dateFilter').on('change', function () {
        attendanceTable.ajax.reload();
    });
});
</script>
@endpush

@push('styles')
<style>
.dt-toolbar .dataTables_filter input,
.dt-toolbar .dataTables_length select,
.dt-toolbar .btn-dt-export,
.dt-toolbar .dt-button { height:34px !important; line-height:1 !important; box-sizing:border-box !important; }
.dt-table { overflow-x:auto; }
.dt-sub   { font-size:.75rem; color:var(--muted); margin-top:2px; }
</style>
@endpush
