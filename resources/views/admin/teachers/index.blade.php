@extends('layouts.app')
@section('title', __('teachers.title'))

@section('content')

{{-- Page Header --}}
<div class="glass-card">
    <div class="card-header">
        <span>
            <i class="fas fa-chalkboard-teacher me-2" style="color:var(--cyan)"></i>
            {{ __('teachers.title') }}
            <span class="badge-count" id="totalCount"
                  style="margin-left:8px;background:rgba(6,182,212,.15);color:var(--cyan);padding:2px 10px;border-radius:20px;font-size:.75rem"></span>
        </span>
        <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#teacherModal" onclick="openAddModal()">
            <i class="fas fa-plus"></i> {{ __('teachers.add_new') }}
        </button>
    </div>

    <table id="teachersTable" class="table-dark-custom" style="width:100%">
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>{{ __('common.name') }}</th>
                <th>{{ __('teachers.qualification') }}</th>
                <th>{{ __('teachers.subject_specialization') }}</th>
                <th>{{ __('teachers.joining_date') }}</th>
                <th>{{ __('common.status') }}</th>
                <th style="width:90px">{{ __('common.actions') }}</th>
            </tr>
        </thead>
    </table>
</div>

{{-- ──────────────────────────────────────────────────────────
     Add / Edit Modal
────────────────────────────────────────────────────────── --}}
<div class="modal fade" id="teacherModal" tabindex="-1" aria-labelledby="teacherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--card);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)">
                <h5 class="modal-title" id="teacherModalLabel" style="color:var(--text)">
                    <i class="fas fa-user-tie me-2" style="color:var(--cyan)"></i>
                    <span id="modalTitleText">{{ __('teachers.add_new') }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="teacherForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" id="teacherId" value="">

                <div class="modal-body">
                    {{-- Photo Preview --}}
                    <div class="text-center mb-3">
                        <div id="photoPreviewWrap" style="display:none;margin-bottom:8px">
                            <img id="photoPreview" src=""
                                 style="width:70px;height:70px;border-radius:50%;object-fit:cover;border:2px solid var(--border)">
                        </div>
                        <label for="photo" style="cursor:pointer;color:var(--cyan);font-size:.82rem">
                            <i class="fas fa-camera me-1"></i>{{ __('teachers.photo') }}
                        </label>
                        <input type="file" id="photo" name="photo" class="d-none"
                               accept="image/jpeg,image/png,image/webp" onchange="previewPhoto(this)">
                    </div>

                    <div class="row g-3">
                        {{-- Full Name --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">
                                {{ __('teachers.full_name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="f_name" class="form-control" required maxlength="100"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">
                                {{ __('teachers.email') }} <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="f_email" class="form-control" required maxlength="100"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Password --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">
                                {{ __('teachers.password') }}
                                <span id="passwordRequired" class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" id="f_password" class="form-control" minlength="6"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                            <div id="passwordHint" class="form-text" style="color:var(--muted);display:none">
                                {{ __('teachers.leave_password') }}
                            </div>
                        </div>
                        {{-- Phone --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('teachers.phone') }}</label>
                            <input type="text" name="phone" id="f_phone" class="form-control" maxlength="20"
                                   placeholder="03xx-xxxxxxx"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Qualification --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('teachers.qualification') }}</label>
                            <input type="text" name="qualification" id="f_qualification" class="form-control"
                                   maxlength="100" placeholder="e.g. M.Sc, B.Ed, MA"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Subject Specialization --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('teachers.subject_specialization') }}</label>
                            <input type="text" name="subject_specialization" id="f_specialization" class="form-control"
                                   maxlength="100" placeholder="e.g. Mathematics, English"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Joining Date --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('teachers.joining_date') }}</label>
                            <input type="date" name="joining_date" id="f_joining_date" class="form-control"
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
// TEACHERS — DataTable + Ajax CRUD
// ──────────────────────────────────────────────────────────────────────────

var teachersTable;

$(document).ready(function () {
    teachersTable = $('#teachersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.teachers.data") }}',
            type: 'GET',
        },
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',    orderable: false, searchable: false },
            { data: 'name',           name: 'users.name' },
            { data: 'qualification',  name: 'qualification' },
            { data: 'specialization', name: 'subject_specialization' },
            { data: 'joining_date',   name: 'joining_date' },
            { data: 'status',         name: 'status',  orderable: false, searchable: false },
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
                                       '<div class="print-meta">{{ __("teachers.title") }} &nbsp;|&nbsp; ' +
                                       'Printed: ' + new Date().toLocaleDateString("en-PK",{day:"2-digit",month:"short",year:"numeric"}) + '</div>';
                    }
                }
            },
        ],
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']],
        language: {
            search:           '',
            searchPlaceholder:'{{ __("teachers.search") }}',
            processing:       '<div class="dt-spinner-card"><i class="fas fa-spinner fa-spin"></i><span>{{ __("common.loading") }}</span></div>',
            emptyTable:       '<div class="dt-empty">{{ __("teachers.no_teachers") }}</div>',
            zeroRecords:      '<div class="dt-empty">{{ __("teachers.no_teachers") }}</div>',
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
            $('#totalCount').text(info.recordsTotal + ' {{ __("teachers.title") }}');
        }
    });
});

// ── Modal: Open Add ──────────────────────────────────────────────────────
function openAddModal() {
    document.getElementById('modalTitleText').textContent = '{{ __("teachers.add_new") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('teacherId').value = '';
    document.getElementById('teacherForm').reset();
    document.getElementById('passwordRequired').style.display = 'inline';
    document.getElementById('passwordHint').style.display = 'none';
    document.getElementById('f_password').required = true;
    document.getElementById('photoPreviewWrap').style.display = 'none';
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
}

// ── Modal: Open Edit ─────────────────────────────────────────────────────
function editTeacher(id) {
    document.getElementById('modalTitleText').textContent = '{{ __("teachers.edit") }}';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('teacherId').value = id;
    document.getElementById('passwordRequired').style.display = 'none';
    document.getElementById('passwordHint').style.display = 'block';
    document.getElementById('f_password').required = false;

    axios.get(`/admin/teachers/${id}/edit`)
        .then(function (res) {
            var t = res.data.teacher;
            document.getElementById('f_name').value          = t.name || '';
            document.getElementById('f_email').value         = t.email || '';
            document.getElementById('f_phone').value         = t.phone || '';
            document.getElementById('f_qualification').value = t.qualification || '';
            document.getElementById('f_specialization').value= t.subject_specialization || '';
            document.getElementById('f_joining_date').value  = t.joining_date || '';
            document.getElementById('f_password').value      = '';

            if (t.avatar) {
                document.getElementById('photoPreview').src = t.avatar;
                document.getElementById('photoPreviewWrap').style.display = 'block';
            } else {
                document.getElementById('photoPreviewWrap').style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('teacherModal')).show();
        })
        .catch(function () { toastError('{{ __("teachers.not_found") }}'); });
}

// ── Form Submit ──────────────────────────────────────────────────────────
document.getElementById('teacherForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var id  = document.getElementById('teacherId').value;
    var url = id ? `/admin/teachers/${id}` : '{{ route("admin.teachers.store") }}';

    ajaxSubmit(this, {
        url: url,
        onSuccess: function () {
            teachersTable.ajax.reload(null, false);
            bootstrap.Modal.getInstance(document.getElementById('teacherModal'))?.hide();
        },
    });
});

// ── Toggle Status ────────────────────────────────────────────────────────
function toggleStatus(id) {
    axios.patch(`/admin/teachers/${id}/toggle`)
        .then(function (res) {
            toastSuccess(res.data.message);
            teachersTable.ajax.reload(null, false);
        })
        .catch(function () { toastError('{{ __("common.error") }}'); });
}

// ── Delete ───────────────────────────────────────────────────────────────
function deleteTeacher(id) {
    confirmDelete(`/admin/teachers/${id}`, function () {
        teachersTable.ajax.reload(null, false);
    });
}

// ── Photo Preview ────────────────────────────────────────────────────────
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('photoPreview').src = e.target.result;
            document.getElementById('photoPreviewWrap').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
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
.dt-name-cell { display:flex; align-items:center; gap:10px; }
.dt-avatar    { width:34px; height:34px; border-radius:50%; object-fit:cover; flex-shrink:0; }
.dt-avatar-placeholder {
    width:34px; height:34px; border-radius:50%;
    background:linear-gradient(135deg,#06b6d4,#0891b2); color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-weight:600; font-size:13px; flex-shrink:0;
}
.dt-name  { font-weight:500; color:var(--text); font-size:.875rem; }
.dt-email { font-size:.75rem; color:var(--muted); }
.dt-sub   { font-size:.75rem; color:var(--muted); }
.dt-actions { display:flex; gap:6px; }
.badge-status { border:none; cursor:pointer; font-size:.72rem; padding:3px 10px; border-radius:20px; font-weight:500; }
.badge-status.active   { background:rgba(16,185,129,.15); color:#10b981; }
.badge-status.inactive { background:rgba(239,68,68,.12);  color:#ef4444; }
.badge-status:hover { opacity:.8; }
</style>
@endpush
