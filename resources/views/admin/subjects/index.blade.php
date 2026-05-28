@extends('layouts.app')
@section('title', __('subjects.title'))

@section('content')

{{-- Page Header --}}
<div class="glass-card">
    <div class="card-header">
        <span>
            <i class="fas fa-book me-2" style="color:var(--yellow)"></i>
            {{ __('subjects.title') }}
            <span class="badge-count" id="totalCount"
                  style="margin-left:8px;background:rgba(245,158,11,.15);color:var(--yellow);padding:2px 10px;border-radius:20px;font-size:.75rem"></span>
        </span>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            {{-- Class Filter --}}
            <select id="classFilter" class="form-select form-select-sm"
                    style="width:170px;background:var(--surface);border-color:var(--border);color:var(--text)">
                <option value="">{{ __('subjects.all_classes') }}</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">
                        {{ $class->name }}{{ $class->section ? ' — '.$class->section : '' }}
                    </option>
                @endforeach
            </select>
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#subjectModal" onclick="openAddModal()">
                <i class="fas fa-plus"></i> {{ __('subjects.add_new') }}
            </button>
        </div>
    </div>

    <table id="subjectsTable" class="table-dark-custom" style="width:100%">
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>{{ __('subjects.subject_name') }}</th>
                <th>{{ __('subjects.code') }}</th>
                <th>{{ __('subjects.class') }}</th>
                <th>{{ __('subjects.assigned_teacher') }}</th>
                <th>{{ __('common.status') }}</th>
                <th style="width:90px">{{ __('common.actions') }}</th>
            </tr>
        </thead>
    </table>
</div>

{{-- Add / Edit Modal --}}
<div class="modal fade" id="subjectModal" tabindex="-1" aria-labelledby="subjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--card);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)">
                <h5 class="modal-title" id="subjectModalLabel" style="color:var(--text)">
                    <i class="fas fa-book me-2" style="color:var(--yellow)"></i>
                    <span id="modalTitleText">{{ __('subjects.add_new') }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="subjectForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" id="subjectId" value="">

                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Subject Name --}}
                        <div class="col-12">
                            <label class="form-label" style="color:var(--text-2)">
                                {{ __('subjects.subject_name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="f_name" class="form-control" required maxlength="100"
                                   placeholder="e.g. Mathematics, English, Urdu"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Subject Code --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('subjects.code') }}</label>
                            <input type="text" name="code" id="f_code" class="form-control" maxlength="20"
                                   placeholder="e.g. MATH, ENG"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Class --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">
                                {{ __('subjects.class') }} <span class="text-danger">*</span>
                            </label>
                            <select name="class_id" id="f_class_id" class="form-select" required
                                    style="background:var(--surface);border-color:var(--border);color:var(--text)">
                                <option value="">{{ __('subjects.select_class') }}</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">
                                        {{ $class->name }}{{ $class->section ? ' — '.$class->section : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Assign Teacher --}}
                        <div class="col-12">
                            <label class="form-label" style="color:var(--text-2)">
                                {{ __('subjects.assign_teacher') }}
                                <span style="color:var(--muted);font-size:.78rem">({{ __('subjects.optional') }})</span>
                            </label>
                            <select name="teacher_id" id="f_teacher_id" class="form-select"
                                    style="background:var(--surface);border-color:var(--border);color:var(--text)">
                                <option value="">— {{ __('subjects.no_teacher') }} —</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t['id'] }}">{{ $t['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="border-color:var(--border)">
                    <button type="button" class="btn-outline-custom" data-bs-dismiss="modal">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit" class="btn-primary-custom" id="submitBtn">
                        <i class="fas fa-save me-1"></i>
                        <span id="submitText">{{ __('common.save') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
var subjectsTable;

$(document).ready(function () {
    subjectsTable = $('#subjectsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.subjects.data") }}',
            type: 'GET',
            data: function (d) { d.class_id = $('#classFilter').val(); }
        },
        columns: [
            { data: 'DT_RowIndex',   name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'subject_name',  name: 'name' },
            { data: 'code',          name: 'code', orderable: false, searchable: false },
            { data: 'class',         name: 'classes.name', orderable: false },
            { data: 'teacher_name',  name: 'teacher_name', orderable: false, searchable: false },
            { data: 'status',        name: 'status', orderable: false, searchable: false },
            { data: 'actions',       name: 'actions', orderable: false, searchable: false },
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
                                       '<div class="print-meta">{{ __("subjects.title") }} &nbsp;|&nbsp; ' +
                                       'Printed: ' + new Date().toLocaleDateString("en-PK",{day:"2-digit",month:"short",year:"numeric"}) + '</div>';
                    }
                }
            },
        ],
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']],
        language: {
            search:           '',
            searchPlaceholder:'{{ __("subjects.search") }}',
            processing:       '<div class="dt-spinner-card"><i class="fas fa-spinner fa-spin"></i><span>{{ __("common.loading") }}</span></div>',
            emptyTable:       '<div class="dt-empty">{{ __("subjects.no_subjects") }}</div>',
            zeroRecords:      '<div class="dt-empty">{{ __("subjects.no_subjects") }}</div>',
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
            $('#totalCount').text(info.recordsTotal + ' {{ __("subjects.title") }}');
        }
    });

    $('#classFilter').on('change', function () { subjectsTable.ajax.reload(); });
});

function openAddModal() {
    document.getElementById('modalTitleText').textContent = '{{ __("subjects.add_new") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('subjectId').value = '';
    document.getElementById('subjectForm').reset();
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
}

function editSubject(id) {
    document.getElementById('modalTitleText').textContent = '{{ __("subjects.edit") }}';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('subjectId').value = id;

    axios.get(`/admin/subjects/${id}/edit`)
        .then(function (res) {
            var s = res.data.subject;
            document.getElementById('f_name').value       = s.name       || '';
            document.getElementById('f_code').value       = s.code       || '';
            document.getElementById('f_class_id').value   = s.class_id   || '';
            document.getElementById('f_teacher_id').value = s.teacher_id || '';
            new bootstrap.Modal(document.getElementById('subjectModal')).show();
        })
        .catch(function () { toastError('{{ __("subjects.not_found") }}'); });
}

document.getElementById('subjectForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var id  = document.getElementById('subjectId').value;
    var url = id ? `/admin/subjects/${id}` : '{{ route("admin.subjects.store") }}';

    ajaxSubmit(this, {
        url: url,
        onSuccess: function () {
            subjectsTable.ajax.reload(null, false);
            bootstrap.Modal.getInstance(document.getElementById('subjectModal'))?.hide();
        },
    });
});

function toggleStatus(id) {
    axios.patch(`/admin/subjects/${id}/toggle`)
        .then(function (res) { toastSuccess(res.data.message); subjectsTable.ajax.reload(null, false); })
        .catch(function () { toastError('{{ __("common.error") }}'); });
}

function deleteSubject(id) {
    confirmDelete(`/admin/subjects/${id}`, function () { subjectsTable.ajax.reload(null, false); });
}
</script>
@endpush

@push('styles')
<style>
.dt-toolbar .dataTables_filter input,
.dt-toolbar .dataTables_length select,
.dt-toolbar .btn-dt-export,
.dt-toolbar .dt-button { height:34px !important; line-height:1 !important; box-sizing:border-box !important; }
.dt-table { overflow-x:auto; }
.dt-actions { display:flex; gap:6px; }
.badge-status { border:none; cursor:pointer; font-size:.72rem; padding:3px 10px; border-radius:20px; font-weight:500; }
.badge-status.active   { background:rgba(16,185,129,.15); color:#10b981; }
.badge-status.inactive { background:rgba(239,68,68,.12);  color:#ef4444; }
.badge-status:hover { opacity:.8; }
</style>
@endpush
