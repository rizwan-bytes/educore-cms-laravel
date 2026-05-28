@extends('layouts.app')
@section('title', __('staff.title'))

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

{{-- Page Header --}}
<div class="glass-card">
    <div class="card-header">
        <span>
            <i class="fas fa-users-gear me-2" style="color:var(--cyan)"></i>
            {{ __('staff.title') }}
            <span class="badge-count" id="totalCount"
                  style="margin-left:8px;background:rgba(6,182,212,.15);color:var(--cyan);padding:2px 10px;border-radius:20px;font-size:.75rem;">{{ $totalStaff }}</span>
        </span>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            {{-- Department Filter --}}
            <select id="deptFilter" class="form-select form-select-sm"
                    style="width:175px;background:var(--surface);border-color:var(--border);color:var(--text)">
                <option value="">{{ __('staff.all_departments') }}</option>
                @foreach($departments as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            {{-- Status Filter --}}
            <select id="statusFilter" class="form-select form-select-sm"
                    style="width:130px;background:var(--surface);border-color:var(--border);color:var(--text)">
                <option value="">{{ __('common.status') }}</option>
                <option value="1">{{ __('common.active') }}</option>
                <option value="0">{{ __('common.inactive') }}</option>
            </select>
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#staffModal" onclick="openAddModal()">
                <i class="fas fa-plus"></i> {{ __('staff.add_new') }}
            </button>
        </div>
    </div>

    <table id="staffTable" class="table-dark-custom" style="width:100%">
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>{{ __('staff.col_name') }}</th>
                <th>{{ __('staff.col_department') }}</th>
                <th>{{ __('staff.col_contact') }}</th>
                <th>{{ __('staff.col_joining') }}</th>
                <th>{{ __('staff.col_salary') }}</th>
                <th>{{ __('common.status') }}</th>
                <th style="width:90px">{{ __('common.actions') }}</th>
            </tr>
        </thead>
    </table>
</div>

{{-- Add / Edit Modal --}}
<div class="modal fade" id="staffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--card);border:1px solid var(--border)">
            <div class="modal-header" style="border-color:var(--border)">
                <h5 class="modal-title" style="color:var(--text)">
                    <i class="fas fa-users-gear me-2" style="color:var(--cyan)"></i>
                    <span id="modalTitleText">{{ __('staff.add_new') }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="staffForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method"  id="formMethod" value="POST">
                <input type="hidden" name="staff_id" id="staffId">

                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Photo Preview --}}
                        <div class="col-12 text-center">
                            <img id="photoPreview" src="https://ui-avatars.com/api/?name=S&background=6366f1&color=fff&size=80&bold=true"
                                 style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--primary);cursor:pointer;"
                                 onclick="document.getElementById('photoInput').click()"
                                 title="Click to change photo">
                            <div class="mt-1" style="font-size:11px;color:var(--text-2)">{{ __('staff.photo') }}</div>
                            <input type="file" id="photoInput" name="photo" accept="image/*" class="d-none">
                        </div>

                        {{-- Name --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('staff.name') }} *</label>
                            <input type="text" name="name" id="staffName" class="form-control" style="background:var(--surface);border-color:var(--border);color:var(--text)"
                                   placeholder="{{ __('staff.name') }}" required>
                        </div>
                        {{-- Phone --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('staff.phone') }}</label>
                            <input type="text" name="phone" id="staffPhone" class="form-control" style="background:var(--surface);border-color:var(--border);color:var(--text)"
                                   placeholder="03xx-xxxxxxx">
                        </div>
                        {{-- CNIC --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('staff.cnic') }}</label>
                            <input type="text" name="cnic" id="staffCnic" class="form-control" style="background:var(--surface);border-color:var(--border);color:var(--text)"
                                   placeholder="xxxxx-xxxxxxx-x">
                        </div>
                        {{-- Department --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('staff.department') }} *</label>
                            <select name="department" id="staffDept" class="form-select" style="background:var(--surface);border-color:var(--border);color:var(--text)" required>
                                <option value="">— {{ __('staff.select_department') }} —</option>
                                @foreach($departments as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Designation --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('staff.designation') }}</label>
                            <input type="text" name="designation" id="staffDesig" class="form-control" style="background:var(--surface);border-color:var(--border);color:var(--text)"
                                   placeholder="{{ __('staff.designation') }}">
                        </div>
                        {{-- Joining Date --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('staff.joining_date') }}</label>
                            <input type="date" name="joining_date" id="staffJoining" class="form-control" style="background:var(--surface);border-color:var(--border);color:var(--text)">
                        </div>
                        {{-- Salary --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('staff.salary') }}</label>
                            <input type="number" name="salary" id="staffSalary" class="form-control" style="background:var(--surface);border-color:var(--border);color:var(--text)"
                                   placeholder="0.00" min="0" step="0.01">
                        </div>
                        {{-- Status --}}
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch ms-2">
                                <input class="form-check-input" type="checkbox" name="status" id="staffStatus" checked>
                                <label class="form-check-label text-muted ms-2" for="staffStatus">
                                    {{ __('common.active') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="border-color:var(--border)">
                    <button type="button" class="btn-outline-custom" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn-primary-custom" id="saveBtn">
                        <i class="fas fa-save me-1"></i> {{ __('common.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── DataTable ────────────────────────────────────────────────────────────
let staffTable;

$(document).ready(function() {
    staffTable = $('#staffTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.staff.data") }}',
            type: 'GET',
            data: function(d) {
                d.department = $('#deptFilter').val();
                d.status     = $('#statusFilter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex',   name: 'DT_RowIndex',   orderable: false, searchable: false },
            { data: 'photo_name',    name: 'name' },
            { data: 'dept_badge',    name: 'department', orderable: false, searchable: false },
            { data: 'contact',       name: 'phone',      orderable: false, searchable: false },
            { data: 'joining',       name: 'joining_date' },
            { data: 'salary_fmt',    name: 'salary',     orderable: true, searchable: false },
            { data: 'status_badge',  name: 'status',     orderable: false, searchable: false },
            { data: 'actions',       name: 'actions',    orderable: false, searchable: false },
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
                    style.innerHTML = 'body{font-family:Arial,sans-serif;background:#fff;color:#111;padding:20px;margin:0}'
                        + 'h1{font-size:16px;font-weight:700;color:#111;margin:0 0 2px}'
                        + '.print-meta{font-size:11px;color:#6b7280;margin-bottom:16px;border-bottom:2px solid #e5e7eb;padding-bottom:10px}'
                        + 'table{border-collapse:collapse;width:100%}'
                        + 'thead th{background:#f3f4f6!important;color:#374151!important;font-weight:700;font-size:10px;text-transform:uppercase;padding:9px 10px;border:1px solid #e5e7eb}'
                        + 'tbody td{padding:8px 10px;border:1px solid #e5e7eb;color:#111;font-size:12px;background:#fff}'
                        + 'tbody tr:nth-child(even) td{background:#f9fafb}'
                        + '@page{margin:15mm}';
                    win.document.head.appendChild(style);
                    var h1 = win.document.querySelector('h1');
                    if (h1) h1.outerHTML = '<h1>EduCore CMS</h1><div class="print-meta">{{ __("staff.title") }} | Printed: '
                        + new Date().toLocaleDateString("en-PK", {day:"2-digit", month:"short", year:"numeric"}) + '</div>';
                }
            }
        ],
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']],
        order: [[1, 'asc']],
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
        drawCallback: function(settings) {
            var info = this.api().page.info();
            $('#totalCount').text(info.recordsTotal);
        }
    });

    // Filter on change
    $('#deptFilter, #statusFilter').on('change', function() {
        staffTable.ajax.reload();
    });
});

// ── Modal ────────────────────────────────────────────────────────────────
function openAddModal() {
    document.getElementById('modalTitleText').textContent = '{{ __("staff.add_new") }}';
    document.getElementById('staffForm').reset();
    document.getElementById('staffId').value   = '';
    document.getElementById('formMethod').value= 'POST';
    document.getElementById('photoPreview').src = 'https://ui-avatars.com/api/?name=S&background=6366f1&color=fff&size=80&bold=true';
}

function editStaff(id) {
    axios.get(`/admin/staff/${id}/edit`)
        .then(res => {
            const s = res.data.staff;
            document.getElementById('modalTitleText').textContent = '{{ __("staff.edit") }}';
            document.getElementById('staffId').value    = s.id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('staffName').value   = s.name ?? '';
            document.getElementById('staffPhone').value  = s.phone ?? '';
            document.getElementById('staffCnic').value   = s.cnic ?? '';
            document.getElementById('staffDept').value   = s.department ?? '';
            document.getElementById('staffDesig').value  = s.designation ?? '';
            document.getElementById('staffJoining').value= s.joining_date ?? '';
            document.getElementById('staffSalary').value = s.salary ?? '';
            document.getElementById('staffStatus').checked = s.status == 1;
            document.getElementById('photoPreview').src = s.photo_url;
            new bootstrap.Modal(document.getElementById('staffModal')).show();
        })
        .catch(() => toastError('{{ __("common.error") }}'));
}

function deleteStaff(id) {
    confirmDelete(`/admin/staff/${id}`, () => staffTable.ajax.reload());
}

function toggleStaff(id) {
    axios.patch(`/admin/staff/${id}/toggle`)
        .then(res => {
            toastSuccess(res.data.message);
            staffTable.ajax.reload(null, false);
        })
        .catch(() => toastError('{{ __("common.error") }}'));
}

// ── Photo Preview ─────────────────────────────────────────────────────────
document.getElementById('photoInput').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => document.getElementById('photoPreview').src = e.target.result;
    reader.readAsDataURL(file);
});

// ── Form Submit ───────────────────────────────────────────────────────────
document.getElementById('staffForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('staffId').value;
    // Always POST — Laravel method spoofing via hidden _method field handles PUT
    const url = id
        ? `/admin/staff/${id}`
        : '{{ route("admin.staff.store") }}';

    ajaxSubmit(this, {
        url,
        method: 'POST', // _method=PUT in FormData handles the spoofing
        onSuccess: () => staffTable.ajax.reload(),
        closeModal: '#staffModal',
        resetForm: true,
    });
});
</script>
@endpush
