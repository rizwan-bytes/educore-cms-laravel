@extends('layouts.app')
@section('title', __('students.title'))

@section('content')

{{-- Page Header --}}
<div class="glass-card">
    <div class="card-header">
        <span>
            <i class="fas fa-user-graduate me-2" style="color:var(--primary-lt)"></i>
            {{ __('students.title') }}
            <span class="badge-count" id="totalCount" style="margin-left:8px;background:rgba(99,102,241,.15);color:var(--primary-lt);padding:2px 10px;border-radius:20px;font-size:.75rem"></span>
        </span>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            {{-- Class Filter --}}
            <select id="classFilter" class="form-select form-select-sm" style="width:160px;background:var(--surface);border-color:var(--border);color:var(--text)">
                <option value="">{{ __('students.all_classes') }}</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">
                        {{ $class->name }}{{ $class->section ? ' — '.$class->section : '' }}
                    </option>
                @endforeach
            </select>
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#studentModal" onclick="openAddModal()">
                <i class="fas fa-plus"></i> {{ __('students.add_new') }}
            </button>
        </div>
    </div>

    <table id="studentsTable" class="table-dark-custom" style="width:100%">
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>{{ __('common.name') }}</th>
                <th>{{ __('students.roll_no') }}</th>
                <th>{{ __('students.class') }}</th>
                <th>{{ __('students.guardian') }}</th>
                <th>{{ __('students.gender') }}</th>
                <th>{{ __('common.status') }}</th>
                <th style="width:90px">{{ __('common.actions') }}</th>
            </tr>
        </thead>
    </table>
</div>

{{-- ──────────────────────────────────────────────────────────
     Add / Edit Modal
────────────────────────────────────────────────────────── --}}
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--card);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)">
                <h5 class="modal-title" id="studentModalLabel" style="color:var(--text)">
                    <i class="fas fa-user-plus me-2" style="color:var(--primary-lt)"></i>
                    <span id="modalTitleText">{{ __('students.add_new') }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="studentForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" id="studentId" value="">

                <div class="modal-body">
                    {{-- Photo Preview --}}
                    <div class="text-center mb-3">
                        <div id="photoPreviewWrap" style="display:none;margin-bottom:8px">
                            <img id="photoPreview" src="" style="width:70px;height:70px;border-radius:50%;object-fit:cover;border:2px solid var(--border)">
                        </div>
                        <label for="photo" style="cursor:pointer;color:var(--primary-lt);font-size:.82rem">
                            <i class="fas fa-camera me-1"></i>{{ __('students.photo') }} ({{ __('common.optional') ?? 'optional' }})
                        </label>
                        <input type="file" id="photo" name="photo" class="d-none" accept="image/jpeg,image/png,image/webp" onchange="previewPhoto(this)">
                    </div>

                    <div class="row g-3">
                        {{-- Full Name --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('students.full_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="f_name" class="form-control" required maxlength="100"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('students.email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="f_email" class="form-control" required maxlength="100"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Password --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">
                                {{ __('students.password') }}
                                <span id="passwordRequired" class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" id="f_password" class="form-control" minlength="6"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                            <div id="passwordHint" class="form-text" style="color:var(--muted);display:none">
                                {{ __('students.leave_password') }}
                            </div>
                        </div>
                        {{-- Roll No --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('students.roll_no') }}</label>
                            <input type="text" name="roll_no" id="f_roll_no" class="form-control" maxlength="30"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Class --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('students.class') }} <span class="text-danger">*</span></label>
                            <select name="class_id" id="f_class_id" class="form-select" required
                                    style="background:var(--surface);border-color:var(--border);color:var(--text)">
                                <option value="">{{ __('students.select_class') }}</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">
                                        {{ $class->name }}{{ $class->section ? ' — '.$class->section : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Gender --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('students.gender') }}</label>
                            <select name="gender" id="f_gender" class="form-select"
                                    style="background:var(--surface);border-color:var(--border);color:var(--text)">
                                <option value="Male">{{ __('students.male') }}</option>
                                <option value="Female">{{ __('students.female') }}</option>
                            </select>
                        </div>
                        {{-- Date of Birth --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('students.date_of_birth') }}</label>
                            <input type="date" name="date_of_birth" id="f_dob" class="form-control"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Admission Date --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('students.admission_date') }}</label>
                            <input type="date" name="admission_date" id="f_admission" class="form-control"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Guardian Name --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('students.guardian') }}</label>
                            <input type="text" name="guardian_name" id="f_guardian" class="form-control" maxlength="100"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Guardian Phone --}}
                        <div class="col-md-6">
                            <label class="form-label" style="color:var(--text-2)">{{ __('students.guardian_phone') }}</label>
                            <input type="text" name="guardian_phone" id="f_guardian_phone" class="form-control" maxlength="20"
                                   style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Address --}}
                        <div class="col-12">
                            <label class="form-label" style="color:var(--text-2)">{{ __('students.address') }}</label>
                            <textarea name="address" id="f_address" class="form-control" rows="2" maxlength="500"
                                      style="background:var(--surface);border-color:var(--border);color:var(--text);resize:vertical"></textarea>
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
// STUDENTS — DataTable + Ajax CRUD
// ──────────────────────────────────────────────────────────────────────────

var studentsTable;
var isEditMode = false;

$(document).ready(function () {
    studentsTable = $('#studentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.students.data") }}',
            type: 'GET',
            data: function (d) {
                d.class_id = $('#classFilter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name',     name: 'users.name' },
            { data: 'roll_no',  name: 'roll_no' },
            { data: 'class',    name: 'classes.name', orderable: false },
            { data: 'guardian', name: 'guardian_name', orderable: false },
            { data: 'gender',   name: 'gender' },
            { data: 'status',   name: 'status', orderable: false, searchable: false },
            { data: 'actions',  name: 'actions', orderable: false, searchable: false },
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
                    // Professional white print layout
                    var style = win.document.createElement('style');
                    style.innerHTML = [
                        'body { font-family: Arial, sans-serif; background:#fff; color:#111; padding:20px; margin:0; }',
                        'h1   { font-size:16px; font-weight:700; color:#111; margin:0 0 2px; }',
                        '.print-meta { font-size:11px; color:#6b7280; margin-bottom:16px; border-bottom:2px solid #e5e7eb; padding-bottom:10px; }',
                        'table { border-collapse:collapse; width:100%; margin-top:0; }',
                        'thead th { background:#f3f4f6 !important; color:#374151 !important; font-weight:700;',
                        '           font-size:10px; text-transform:uppercase; letter-spacing:.04em;',
                        '           padding:9px 10px; border:1px solid #e5e7eb; }',
                        'tbody td { padding:8px 10px; border:1px solid #e5e7eb; color:#111; font-size:12px; background:#fff; }',
                        'tbody tr:nth-child(even) td { background:#f9fafb; }',
                        'tfoot { display:none; }',
                        '@page { margin:15mm; }'
                    ].join(' ');
                    win.document.head.appendChild(style);
                    // Replace h1 with school name + print info
                    var h1 = win.document.querySelector('h1');
                    if (h1) {
                        h1.outerHTML = '<h1>{{ \App\Services\SettingService::get("college_name", "EduCore CMS") }}</h1>' +
                                       '<div class="print-meta">{{ __("students.title") }} &nbsp;|&nbsp; ' +
                                       'Printed: ' + new Date().toLocaleDateString("en-PK",{day:"2-digit",month:"short",year:"numeric"}) + '</div>';
                    }
                }
            },
        ],
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']],
        language: {
            search:       '',
            searchPlaceholder: '{{ __("students.search") }}',
            processing:   '<div class="dt-spinner-card"><i class="fas fa-spinner fa-spin"></i><span>{{ __("common.loading") }}</span></div>',
            emptyTable:   '<div class="dt-empty">{{ __("students.no_students") }}</div>',
            zeroRecords:  '<div class="dt-empty">{{ __("students.no_students") }}</div>',
            info:         'Showing _START_–_END_ of _TOTAL_',
            infoEmpty:    'Showing 0 of 0',
            infoFiltered: '(filtered from _MAX_)',
            paginate: {
                first:    '<i class="fas fa-angles-left"></i>',
                last:     '<i class="fas fa-angles-right"></i>',
                previous: '<i class="fas fa-chevron-left"></i>',
                next:     '<i class="fas fa-chevron-right"></i>',
            },
        },
        drawCallback: function (settings) {
            var info = this.api().page.info();
            $('#totalCount').text(info.recordsTotal + ' {{ __("students.title") }}');
        }
    });

    // Class filter change → reload table
    $('#classFilter').on('change', function () {
        studentsTable.ajax.reload();
    });
});

// ── Modal: Open Add ──────────────────────────────────────────────────────
function openAddModal() {
    isEditMode = false;
    document.getElementById('studentModalLabel').querySelector('i').className = 'fas fa-user-plus me-2';
    document.getElementById('studentModalLabel').querySelector('i').style.color = 'var(--primary-lt)';
    document.getElementById('modalTitleText').textContent = '{{ __("students.add_new") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('studentId').value = '';
    document.getElementById('studentForm').reset();
    document.getElementById('passwordRequired').style.display = 'inline';
    document.getElementById('passwordHint').style.display = 'none';
    document.getElementById('f_password').required = true;
    document.getElementById('photoPreviewWrap').style.display = 'none';
    document.getElementById('photoPreview').src = '';
    // Clear any validation errors
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
}

// ── Modal: Open Edit ─────────────────────────────────────────────────────
function editStudent(id) {
    isEditMode = true;
    document.getElementById('modalTitleText').textContent = '{{ __("students.edit") }}';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('studentId').value = id;
    document.getElementById('passwordRequired').style.display = 'none';
    document.getElementById('passwordHint').style.display = 'block';
    document.getElementById('f_password').required = false;

    axios.get(`/admin/students/${id}/edit`)
        .then(function (res) {
            var s = res.data.student;
            document.getElementById('f_name').value          = s.name || '';
            document.getElementById('f_email').value         = s.email || '';
            document.getElementById('f_password').value      = '';
            document.getElementById('f_roll_no').value       = s.roll_no || '';
            document.getElementById('f_class_id').value      = s.class_id || '';
            document.getElementById('f_gender').value        = s.gender || 'Male';
            document.getElementById('f_dob').value           = s.date_of_birth || '';
            document.getElementById('f_admission').value     = s.admission_date || '';
            document.getElementById('f_guardian').value      = s.guardian_name || '';
            document.getElementById('f_guardian_phone').value= s.guardian_phone || '';
            document.getElementById('f_address').value       = s.address || '';

            // Avatar preview
            if (s.avatar) {
                document.getElementById('photoPreview').src = s.avatar;
                document.getElementById('photoPreviewWrap').style.display = 'block';
            } else {
                document.getElementById('photoPreviewWrap').style.display = 'none';
            }

            // Open modal
            new bootstrap.Modal(document.getElementById('studentModal')).show();
        })
        .catch(function () {
            toastError('{{ __("students.not_found") }}');
        });
}

// ── Form Submit (Add + Edit) ─────────────────────────────────────────────
document.getElementById('studentForm').addEventListener('submit', function (e) {
    e.preventDefault();

    var id  = document.getElementById('studentId').value;
    var url = id
        ? `/admin/students/${id}`
        : '{{ route("admin.students.store") }}';

    ajaxSubmit(this, {
        url: url,
        onSuccess: function () {
            studentsTable.ajax.reload(null, false);
            bootstrap.Modal.getInstance(document.getElementById('studentModal'))?.hide();
        },
    });
});

// ── Toggle Status ────────────────────────────────────────────────────────
function toggleStatus(id) {
    axios.patch(`/admin/students/${id}/toggle`)
        .then(function (res) {
            toastSuccess(res.data.message);
            studentsTable.ajax.reload(null, false);
        })
        .catch(function () {
            toastError('{{ __("common.error") }}');
        });
}

// ── Delete ───────────────────────────────────────────────────────────────
function deleteStudent(id) {
    confirmDelete(`/admin/students/${id}`, function () {
        studentsTable.ajax.reload(null, false);
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
/* ── Force same height for all toolbar elements ─────── */
.dt-toolbar .dataTables_filter input,
.dt-toolbar .dataTables_length select,
.dt-toolbar .btn-dt-export,
.dt-toolbar .dt-button {
    height: 34px !important;
    line-height: 1 !important;
    box-sizing: border-box !important;
}
/* ── DataTable table wrapper ───────────────────────── */
.dt-table { overflow-x: auto; }

/* ── Name cell ─────────────────────────────────────── */
.dt-name-cell { display:flex; align-items:center; gap:10px; }
.dt-avatar    { width:34px; height:34px; border-radius:50%; object-fit:cover; flex-shrink:0; }
.dt-avatar-placeholder {
    width:34px; height:34px; border-radius:50%;
    background: var(--primary-grad); color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-weight:600; font-size:13px; flex-shrink:0;
}
.dt-name  { font-weight:500; color:var(--text); font-size:.875rem; }
.dt-email { font-size:.75rem; color:var(--muted); }
.dt-sub   { font-size:.75rem; color:var(--muted); }
.dt-actions { display:flex; gap:6px; }

/* ── Status badge as button ────────────────────────── */
.badge-status { border:none; cursor:pointer; font-size:.72rem; padding:3px 10px; border-radius:20px; font-weight:500; }
.badge-status.active   { background:rgba(16,185,129,.15); color:#10b981; }
.badge-status.inactive { background:rgba(239,68,68,.12);  color:#ef4444; }
.badge-status:hover { opacity:.8; }
</style>
@endpush
