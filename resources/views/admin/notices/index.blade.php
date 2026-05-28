@extends('layouts.app')
@section('title', __('notices.title'))

@section('content')

{{-- Page Header --}}
<div class="glass-card">
    <div class="card-header">
        <span>
            <i class="fas fa-bullhorn me-2" style="color:var(--orange)"></i>
            {{ __('notices.title') }}
            <span class="badge-count" id="totalCount"
                  style="margin-left:8px;background:rgba(249,115,22,.15);color:var(--orange);padding:2px 10px;border-radius:20px;font-size:.75rem"></span>
        </span>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            {{-- Target Role Filter --}}
            <select id="roleFilter" class="form-select form-select-sm"
                    style="width:150px;background:var(--surface);border-color:var(--border);color:var(--text)">
                <option value="">{{ __('notices.all_roles') }}</option>
                <option value="all">{{ __('notices.role_all') }}</option>
                <option value="teacher">{{ __('notices.role_teacher') }}</option>
                <option value="student">{{ __('notices.role_student') }}</option>
                <option value="admin">{{ __('notices.role_admin') }}</option>
            </select>
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#noticeModal" onclick="openAddModal()">
                <i class="fas fa-plus"></i> {{ __('notices.add_new') }}
            </button>
        </div>
    </div>

    <table id="noticesTable" class="table-dark-custom" style="width:100%">
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>{{ __('notices.notice_title') }}</th>
                <th>{{ __('notices.target_role') }}</th>
                <th>{{ __('notices.posted_by') }}</th>
                <th>{{ __('notices.posted_on') }}</th>
                <th>{{ __('common.status') }}</th>
                <th style="width:90px">{{ __('common.actions') }}</th>
            </tr>
        </thead>
    </table>
</div>

{{-- Add / Edit Modal --}}
<div class="modal fade" id="noticeModal" tabindex="-1" aria-labelledby="noticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--card);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)">
                <h5 class="modal-title" id="noticeModalLabel" style="color:var(--text)">
                    <i class="fas fa-bullhorn me-2" style="color:var(--orange)"></i>
                    <span id="modalTitleText">{{ __('notices.add_new') }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="noticeForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" id="noticeId" value="">

                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Notice Title --}}
                        <div class="col-md-8">
                            <label class="form-label" style="color:var(--text-2)">
                                {{ __('notices.notice_title') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="f_title" class="form-control" required maxlength="200"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Target Role --}}
                        <div class="col-md-4">
                            <label class="form-label" style="color:var(--text-2)">
                                {{ __('notices.target_role') }} <span class="text-danger">*</span>
                            </label>
                            <select name="target_role" id="f_target_role" class="form-select" required
                                    style="background:var(--surface);border-color:var(--border);color:var(--text)">
                                <option value="all">{{ __('notices.role_all') }}</option>
                                <option value="teacher">{{ __('notices.role_teacher') }}</option>
                                <option value="student">{{ __('notices.role_student') }}</option>
                                <option value="admin">{{ __('notices.role_admin') }}</option>
                            </select>
                        </div>
                        {{-- Content --}}
                        <div class="col-12">
                            <label class="form-label" style="color:var(--text-2)">
                                {{ __('notices.content') }} <span class="text-danger">*</span>
                            </label>
                            <textarea name="content" id="f_content" class="form-control" rows="5" required
                                      style="background:var(--surface);border-color:var(--border);color:var(--text);resize:vertical"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="border-color:var(--border)">
                    <button type="button" class="btn-outline-custom" data-bs-dismiss="modal">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit" class="btn-primary-custom" id="submitBtn">
                        <i class="fas fa-paper-plane me-1"></i>
                        <span id="submitText">{{ __('notices.add_new') }}</span>
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
// NOTICES — DataTable + Ajax CRUD
// ──────────────────────────────────────────────────────────────────────────

var noticesTable;

$(document).ready(function () {
    noticesTable = $('#noticesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.notices.data") }}',
            type: 'GET',
            data: function (d) {
                d.target_role = $('#roleFilter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'title',       name: 'title' },
            { data: 'target',      name: 'target_role', orderable: false, searchable: false },
            { data: 'posted_by',   name: 'posted_by', orderable: false, searchable: false },
            { data: 'posted_on',   name: 'created_at' },
            { data: 'status',      name: 'status', orderable: false, searchable: false },
            { data: 'actions',     name: 'actions', orderable: false, searchable: false },
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
                                       '<div class="print-meta">{{ __("notices.title") }} &nbsp;|&nbsp; ' +
                                       'Printed: ' + new Date().toLocaleDateString("en-PK",{day:"2-digit",month:"short",year:"numeric"}) + '</div>';
                    }
                }
            },
        ],
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']],
        language: {
            search:           '',
            searchPlaceholder:'{{ __("notices.search") }}',
            processing:       '<div class="dt-spinner-card"><i class="fas fa-spinner fa-spin"></i><span>{{ __("common.loading") }}</span></div>',
            emptyTable:       '<div class="dt-empty">{{ __("notices.no_notices") }}</div>',
            zeroRecords:      '<div class="dt-empty">{{ __("notices.no_notices") }}</div>',
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
            $('#totalCount').text(info.recordsTotal + ' {{ __("notices.title") }}');
        }
    });

    $('#roleFilter').on('change', function () {
        noticesTable.ajax.reload();
    });
});

// ── Modal: Open Add ──────────────────────────────────────────────────────
function openAddModal() {
    document.getElementById('modalTitleText').textContent = '{{ __("notices.add_new") }}';
    document.getElementById('submitText').textContent = '{{ __("notices.add_new") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('noticeId').value = '';
    document.getElementById('noticeForm').reset();
    document.getElementById('f_target_role').value = 'all';
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
}

// ── Modal: Open Edit ─────────────────────────────────────────────────────
function editNotice(id) {
    document.getElementById('modalTitleText').textContent = '{{ __("notices.edit") }}';
    document.getElementById('submitText').textContent = '{{ __("common.save") }}';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('noticeId').value = id;

    axios.get(`/admin/notices/${id}/edit`)
        .then(function (res) {
            var n = res.data.notice;
            document.getElementById('f_title').value       = n.title || '';
            document.getElementById('f_content').value     = n.content || '';
            document.getElementById('f_target_role').value = n.target_role || 'all';
            new bootstrap.Modal(document.getElementById('noticeModal')).show();
        })
        .catch(function () { toastError('{{ __("notices.not_found") }}'); });
}

// ── Form Submit ──────────────────────────────────────────────────────────
document.getElementById('noticeForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var id  = document.getElementById('noticeId').value;
    var url = id ? `/admin/notices/${id}` : '{{ route("admin.notices.store") }}';

    ajaxSubmit(this, {
        url: url,
        onSuccess: function () {
            noticesTable.ajax.reload(null, false);
            bootstrap.Modal.getInstance(document.getElementById('noticeModal'))?.hide();
        },
    });
});

// ── Toggle Status ────────────────────────────────────────────────────────
function toggleStatus(id) {
    axios.patch(`/admin/notices/${id}/toggle`)
        .then(function (res) {
            toastSuccess(res.data.message);
            noticesTable.ajax.reload(null, false);
        })
        .catch(function () { toastError('{{ __("common.error") }}'); });
}

// ── Delete ───────────────────────────────────────────────────────────────
function deleteNotice(id) {
    confirmDelete(`/admin/notices/${id}`, function () {
        noticesTable.ajax.reload(null, false);
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
.dt-sub   { font-size:.75rem; color:var(--muted); margin-top:2px; }
.dt-actions { display:flex; gap:6px; }
.badge-status { border:none; cursor:pointer; font-size:.72rem; padding:3px 10px; border-radius:20px; font-weight:500; }
.badge-status.active   { background:rgba(16,185,129,.15); color:#10b981; }
.badge-status.inactive { background:rgba(239,68,68,.12);  color:#ef4444; }
.badge-status:hover { opacity:.8; }
</style>
@endpush
