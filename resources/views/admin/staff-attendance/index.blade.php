@extends('layouts.app')
@section('title', __('staff.attendance_history'))

@push('styles')
<style>
.dt-toolbar .dataTables_filter input,
.dt-toolbar .dataTables_length select,
.dt-toolbar .btn-dt-export,
.dt-toolbar .dt-button { height:34px!important;line-height:1!important;box-sizing:border-box!important; }
.dt-table { overflow-x:auto; }
</style>
@endpush

@section('content')

<div class="glass-card">
    <div class="card-header">
        <span>
            <i class="fas fa-calendar-check me-2" style="color:var(--green)"></i>
            {{ __('staff.attendance_history') }}
        </span>
        <a href="{{ route('admin.staff-attendance.mark') }}" class="btn-primary-custom btn-sm">
            <i class="fas fa-clipboard-check me-1"></i> {{ __('staff.mark_attendance') }}
        </a>
    </div>

    {{-- Filters --}}
    <div class="card-body pb-1">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-muted" style="font-size:12px">{{ __('staff.department') }}</label>
                <select id="deptFilter" class="form-select form-select-sm"
                        style="background:var(--surface);border-color:var(--border);color:var(--text)">
                    <option value="">{{ __('staff.all_departments') }}</option>
                    @foreach($departments as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted" style="font-size:12px">{{ __('common.date') }}</label>
                <input type="date" id="dateFilter" class="form-control form-control-sm"
                       style="background:var(--surface);border-color:var(--border);color:var(--text)">
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted" style="font-size:12px">{{ __('common.status') }}</label>
                <select id="statusFilter" class="form-select form-select-sm"
                        style="background:var(--surface);border-color:var(--border);color:var(--text)">
                    <option value="">{{ __('staff.all_statuses') }}</option>
                    <option value="Present">{{ __('staff.status_present') }}</option>
                    <option value="Absent">{{ __('staff.status_absent') }}</option>
                    <option value="Late">{{ __('staff.status_late') }}</option>
                    <option value="Half_Day">{{ __('staff.status_half_day') }}</option>
                    <option value="Leave">{{ __('staff.status_leave') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn-outline-custom btn-sm w-100" onclick="resetFilters()">
                    <i class="fas fa-rotate-left me-1"></i> {{ __('common.reset') }}
                </button>
            </div>
        </div>
    </div>

    <table id="attTable" class="table-dark-custom" style="width:100%">
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>{{ __('staff.col_name') }}</th>
                <th>{{ __('staff.col_department') }}</th>
                <th>{{ __('staff.col_date') }}</th>
                <th>{{ __('common.status') }}</th>
                <th>{{ __('staff.col_marked_by') }}</th>
                <th>{{ __('staff.col_remarks') }}</th>
            </tr>
        </thead>
    </table>
</div>

@endsection

@push('scripts')
<script>
let attTable;

$(document).ready(function() {
    attTable = $('#attTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.staff-attendance.data") }}',
            type: 'GET',
            data: function(d) {
                d.department = $('#deptFilter').val();
                d.date       = $('#dateFilter').val();
                d.status     = $('#statusFilter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',  orderable:false, searchable:false },
            { data: 'staff_info',     name: 'staff.name' },
            { data: 'dept_badge',     name: 'department', orderable:false, searchable:false },
            { data: 'date_fmt',       name: 'date' },
            { data: 'status_badge',   name: 'status', orderable:false, searchable:false },
            { data: 'marked_by_name', name: 'marker.name', orderable:false, searchable:false },
            { data: 'remarks',        name: 'remarks', orderable:false, searchable:false },
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
                text: '<i class="fas fa-print me-1"></i>Print',
                exportOptions: { columns: ':not(:last-child)' },
                customize: function(win) {
                    var style = win.document.createElement('style');
                    style.innerHTML = 'body{font-family:Arial,sans-serif;background:#fff;color:#111;padding:20px}'
                        + 'h1{font-size:16px;font-weight:700;margin:0 0 2px}'
                        + '.print-meta{font-size:11px;color:#6b7280;margin-bottom:16px;border-bottom:2px solid #e5e7eb;padding-bottom:10px}'
                        + 'table{border-collapse:collapse;width:100%}'
                        + 'thead th{background:#f3f4f6!important;color:#374151!important;font-weight:700;font-size:10px;text-transform:uppercase;padding:9px 10px;border:1px solid #e5e7eb}'
                        + 'tbody td{padding:8px 10px;border:1px solid #e5e7eb;font-size:12px}'
                        + 'tbody tr:nth-child(even) td{background:#f9fafb}'
                        + '@page{margin:15mm}';
                    win.document.head.appendChild(style);
                    var h1 = win.document.querySelector('h1');
                    if (h1) h1.outerHTML = '<h1>EduCore CMS</h1><div class="print-meta">{{ __("staff.attendance_history") }} | Printed: '
                        + new Date().toLocaleDateString("en-PK",{day:"2-digit",month:"short",year:"numeric"}) + '</div>';
                }
            }
        ],
        pageLength: 20,
        lengthMenu: [[10,20,50,100,-1],[10,20,50,100,'All']],
        order: [[3, 'desc']],
        language: {
            search: '',
            searchPlaceholder: '{{ __("common.search") }}...',
            processing:  '<div class="dt-spinner-card"><i class="fas fa-spinner fa-spin"></i><span>{{ __("common.loading") }}</span></div>',
            emptyTable:  '<div class="dt-empty">{{ __("common.no_data") }}</div>',
            zeroRecords: '<div class="dt-empty">{{ __("common.no_data") }}</div>',
            info:        'Showing _START_–_END_ of _TOTAL_',
            infoEmpty:   'Showing 0 of 0',
            infoFiltered:'(filtered from _MAX_)',
            paginate: {
                first:    '<i class="fas fa-angles-left"></i>',
                last:     '<i class="fas fa-angles-right"></i>',
                previous: '<i class="fas fa-chevron-left"></i>',
                next:     '<i class="fas fa-chevron-right"></i>',
            },
        },
    });

    $('#deptFilter, #statusFilter').on('change', () => attTable.ajax.reload());
    $('#dateFilter').on('change', () => attTable.ajax.reload());
});

function resetFilters() {
    document.getElementById('deptFilter').value   = '';
    document.getElementById('dateFilter').value   = '';
    document.getElementById('statusFilter').value = '';
    attTable.ajax.reload();
}
</script>
@endpush
