@extends('layouts.app')
@section('title', __('classes.title'))

@section('content')

{{-- Page Header --}}
<div class="glass-card">
    <div class="card-header">
        <span>
            <i class="fas fa-school me-2" style="color:var(--green)"></i>
            {{ __('classes.title') }}
            <span class="badge-count" id="totalCount"
                  style="margin-left:8px;background:rgba(16,185,129,.15);color:var(--green);padding:2px 10px;border-radius:20px;font-size:.75rem"></span>
        </span>
        <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#classModal" onclick="openAddModal()">
            <i class="fas fa-plus"></i> {{ __('classes.add_new') }}
        </button>
    </div>

    <table id="classesTable" class="table-dark-custom" style="width:100%">
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>{{ __('classes.class_name') }}</th>
                <th>{{ __('classes.section') }}</th>
                <th>{{ __('classes.students_count') }}</th>
                <th>{{ __('common.status') }}</th>
                <th style="width:90px">{{ __('common.actions') }}</th>
            </tr>
        </thead>
    </table>
</div>

{{-- Add / Edit Modal --}}
<div class="modal fade" id="classModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--card);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)">
                <h5 class="modal-title" id="classModalLabel" style="color:var(--text)">
                    <i class="fas fa-school me-2" style="color:var(--green)"></i>
                    <span id="modalTitleText">{{ __('classes.add_new') }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="classForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" id="classId" value="">

                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Class Name --}}
                        <div class="col-12">
                            <label class="form-label" style="color:var(--text-2)">
                                {{ __('classes.class_name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="f_name" class="form-control" required maxlength="100"
                                   placeholder="e.g. Class 1, Grade 8, KG"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Section --}}
                        <div class="col-12">
                            <label class="form-label" style="color:var(--text-2)">
                                {{ __('classes.section') }}
                                <span style="color:var(--muted);font-size:.78rem">({{ __('classes.section_hint') }})</span>
                            </label>
                            <input type="text" name="section" id="f_section" class="form-control" maxlength="50"
                                   placeholder="{{ __('classes.section_hint') }}"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
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
// ──────────────────────────────────────────────────────────────────────────
// CLASSES — DataTable + Ajax CRUD
// ──────────────────────────────────────────────────────────────────────────

var classesTable;

$(document).ready(function () {
    classesTable = $('#classesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.classes.data") }}',
            type: 'GET',
        },
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',     orderable: false, searchable: false },
            { data: 'class_name',     name: 'name' },
            { data: 'section',        name: 'section' },
            { data: 'students_count', name: 'students_count', orderable: false, searchable: false },
            { data: 'status',         name: 'status', orderable: false, searchable: false },
            { data: 'actions',        name: 'actions', orderable: false, searchable: false },
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
                                       '<div class="print-meta">{{ __("classes.title") }} &nbsp;|&nbsp; ' +
                                       'Printed: ' + new Date().toLocaleDateString("en-PK",{day:"2-digit",month:"short",year:"numeric"}) + '</div>';
                    }
                }
            },
        ],
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']],
        language: {
            search:           '',
            searchPlaceholder:'{{ __("classes.search") }}',
            processing:       '<div class="dt-spinner-card"><i class="fas fa-spinner fa-spin"></i><span>{{ __("common.loading") }}</span></div>',
            emptyTable:       '<div class="dt-empty">{{ __("classes.no_classes") }}</div>',
            zeroRecords:      '<div class="dt-empty">{{ __("classes.no_classes") }}</div>',
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
            $('#totalCount').text(info.recordsTotal + ' {{ __("classes.title") }}');
        }
    });
});

// ── Modal: Open Add ──────────────────────────────────────────────────────
function openAddModal() {
    document.getElementById('modalTitleText').textContent = '{{ __("classes.add_new") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('classId').value = '';
    document.getElementById('classForm').reset();
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
}

// ── Modal: Open Edit ─────────────────────────────────────────────────────
function editClass(id) {
    document.getElementById('modalTitleText').textContent = '{{ __("classes.edit") }}';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('classId').value = id;

    axios.get(`/admin/classes/${id}/edit`)
        .then(function (res) {
            var c = res.data.class;
            document.getElementById('f_name').value    = c.name || '';
            document.getElementById('f_section').value = c.section || '';
            new bootstrap.Modal(document.getElementById('classModal')).show();
        })
        .catch(function () { toastError('{{ __("classes.not_found") }}'); });
}

// ── Form Submit ──────────────────────────────────────────────────────────
document.getElementById('classForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var id  = document.getElementById('classId').value;
    var url = id ? `/admin/classes/${id}` : '{{ route("admin.classes.store") }}';

    ajaxSubmit(this, {
        url: url,
        onSuccess: function () {
            classesTable.ajax.reload(null, false);
            bootstrap.Modal.getInstance(document.getElementById('classModal'))?.hide();
        },
    });
});

// ── Toggle Status ────────────────────────────────────────────────────────
function toggleStatus(id) {
    axios.patch(`/admin/classes/${id}/toggle`)
        .then(function (res) {
            toastSuccess(res.data.message);
            classesTable.ajax.reload(null, false);
        })
        .catch(function () { toastError('{{ __("common.error") }}'); });
}

// ── Delete ───────────────────────────────────────────────────────────────
function deleteClass(id) {
    confirmDelete(`/admin/classes/${id}`, function () {
        classesTable.ajax.reload(null, false);
    });
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
