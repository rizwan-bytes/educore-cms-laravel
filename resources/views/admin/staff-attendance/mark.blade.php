@extends('layouts.app')
@section('title', __('staff.mark_attendance'))

@push('styles')
<style>
/* ── Status Button Grid ───────────────────────────────── */
.att-status-btn {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s;
}
.att-status-btn[data-status="Present"]  { background: rgba(16,185,129,.12); color: var(--green);  border-color: rgba(16,185,129,.3); }
.att-status-btn[data-status="Absent"]   { background: rgba(239,68,68,.12);  color: var(--red);    border-color: rgba(239,68,68,.3); }
.att-status-btn[data-status="Late"]     { background: rgba(245,158,11,.12); color: var(--yellow); border-color: rgba(245,158,11,.3); }
.att-status-btn[data-status="Half_Day"] { background: rgba(249,115,22,.12); color: var(--orange); border-color: rgba(249,115,22,.3); }
.att-status-btn[data-status="Leave"]    { background: rgba(6,182,212,.12);  color: var(--cyan);   border-color: rgba(6,182,212,.3); }

.att-status-btn.active[data-status="Present"]  { background: rgba(16,185,129,.35);  border-color: var(--green);  box-shadow:0 0 0 2px rgba(16,185,129,.25); }
.att-status-btn.active[data-status="Absent"]   { background: rgba(239,68,68,.35);   border-color: var(--red);    box-shadow:0 0 0 2px rgba(239,68,68,.25); }
.att-status-btn.active[data-status="Late"]     { background: rgba(245,158,11,.35);  border-color: var(--yellow); box-shadow:0 0 0 2px rgba(245,158,11,.25); }
.att-status-btn.active[data-status="Half_Day"] { background: rgba(249,115,22,.35);  border-color: var(--orange); box-shadow:0 0 0 2px rgba(249,115,22,.25); }
.att-status-btn.active[data-status="Leave"]    { background: rgba(6,182,212,.35);   border-color: var(--cyan);   box-shadow:0 0 0 2px rgba(6,182,212,.25); }

/* ── Staff Row ────────────────────────────────────────── */
.staff-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.staff-row:hover { background: var(--card-hover); }
.staff-row:last-child { border-bottom: none; }
.staff-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--border);
    flex-shrink: 0;
}
.staff-info { flex: 1; min-width: 0; }
.staff-name { font-weight: 600; font-size: 14px; color: var(--text); }
.staff-desig { font-size: 12px; color: var(--text-2); }
.status-btns { display: flex; gap: 6px; flex-wrap: wrap; }
.remarks-input {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 6px;
    color: var(--text);
    font-size: 12px;
    padding: 4px 8px;
    width: 100%;
    max-width: 180px;
}
.remarks-input:focus { border-color: var(--primary); outline: none; }

/* ── Counters ─────────────────────────────────────────── */
.counter-wrap {
    display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px;
}
.counter-chip {
    display: flex; align-items: center; gap: 6px;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 20px; padding: 5px 14px; font-size: 13px;
}
.counter-chip strong { font-size: 16px; font-weight: 700; }
</style>
@endpush

@section('content')

{{-- Selector Card ──────────────────────────────────────────────────────── --}}
<div class="glass-card mb-4">
    <div class="card-header">
        <span>
            <i class="fas fa-clipboard-check me-2" style="color:var(--green)"></i>
            {{ __('staff.mark_attendance') }}
        </span>
        <a href="{{ route('admin.staff-attendance.index') }}" class="btn-outline-custom btn-sm">
            <i class="fas fa-history me-1"></i> {{ __('staff.attendance_history') }}
        </a>
    </div>
    <div class="card-body pb-2">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-muted">{{ __('staff.department') }} *</label>
                <select id="deptSelect" class="form-select" style="background:var(--surface);border-color:var(--border);color:var(--text)">
                    <option value="">— {{ __('staff.select_department') }} —</option>
                    @foreach($departments as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted">{{ __('common.date') }} *</label>
                <input type="date" id="dateSelect" class="form-control" style="background:var(--surface);border-color:var(--border);color:var(--text)"
                       value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <button id="loadBtn" class="btn-primary-custom w-100" onclick="loadStaff()">
                    <i class="fas fa-users me-2"></i> {{ __('staff.load_staff') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Attendance Card ────────────────────────────────────────────────────── --}}
<div class="glass-card" id="attendanceCard" style="display:none">
    <div class="card-header">
        <span>
            <i class="fas fa-list-check me-2" style="color:var(--cyan)"></i>
            <span id="deptLabel">—</span>
            &nbsp;—&nbsp;
            <span id="dateLabel">—</span>
        </span>
        <div class="d-flex gap-2 align-items-center">
            {{-- Bulk Buttons --}}
            <button class="btn-outline-custom btn-sm" onclick="markAll('Present')">
                <i class="fas fa-check-double me-1" style="color:var(--green)"></i> {{ __('staff.status_present') }}
            </button>
            <button class="btn-outline-custom btn-sm" onclick="markAll('Absent')">
                <i class="fas fa-times me-1" style="color:var(--red)"></i> {{ __('staff.status_absent') }}
            </button>
        </div>
    </div>

    {{-- Already-marked Banner --}}
    <div id="alreadyMarkedBanner" style="display:none;margin:0;padding:12px 20px;
         background:rgba(245,158,11,.1);border-bottom:1px solid rgba(245,158,11,.2);">
        <i class="fas fa-info-circle me-2" style="color:var(--yellow)"></i>
        <span style="color:var(--yellow);font-size:13px;">{{ __('staff.already_marked') }}</span>
    </div>

    {{-- Counters --}}
    <div class="card-body pb-1">
        <div class="counter-wrap" id="counters">
            <div class="counter-chip"><span style="color:var(--green)">●</span> <strong id="cntPresent">0</strong> {{ __('staff.status_present') }}</div>
            <div class="counter-chip"><span style="color:var(--red)">●</span>   <strong id="cntAbsent">0</strong>  {{ __('staff.status_absent') }}</div>
            <div class="counter-chip"><span style="color:var(--yellow)">●</span><strong id="cntLate">0</strong>    {{ __('staff.status_late') }}</div>
            <div class="counter-chip"><span style="color:var(--orange)">●</span><strong id="cntHalf">0</strong>    {{ __('staff.status_half_day') }}</div>
            <div class="counter-chip"><span style="color:var(--cyan)">●</span>  <strong id="cntLeave">0</strong>   {{ __('staff.status_leave') }}</div>
        </div>
    </div>

    {{-- Staff Rows --}}
    <div id="staffList"></div>

    {{-- Save Button --}}
    <div class="card-body pt-2">
        <button class="btn-primary-custom w-100" id="saveBtn" onclick="saveAttendance()">
            <i class="fas fa-save me-2"></i> {{ __('common.save') }} {{ __('staff.attendance') }}
        </button>
    </div>
</div>

{{-- Empty State --}}
<div id="emptyState" class="glass-card text-center py-5" style="display:none">
    <i class="fas fa-user-slash fa-3x mb-3" style="color:var(--muted)"></i>
    <p style="color:var(--text-2)">{{ __('staff.no_staff_found') }}</p>
</div>

@endsection

@push('scripts')
<script>
const STATUSES = ['Present', 'Absent', 'Late', 'Half_Day', 'Leave'];
const STATUS_LABELS = {
    Present:  '{{ __("staff.status_present") }}',
    Absent:   '{{ __("staff.status_absent") }}',
    Late:     '{{ __("staff.status_late") }}',
    Half_Day: '{{ __("staff.status_half_day") }}',
    Leave:    '{{ __("staff.status_leave") }}',
};
const DEPT_LABELS = @json($departments);

let attendanceData = {};

// ── Load Staff ───────────────────────────────────────────────────────────
function loadStaff() {
    const dept = document.getElementById('deptSelect').value;
    const date = document.getElementById('dateSelect').value;

    if (!dept) { toastError('{{ __("staff.select_department") }}'); return; }
    if (!date) { toastError('{{ __("common.date") }} {{ __("common.required") ?? "required" }}'); return; }

    const btn = document.getElementById('loadBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __("common.loading") }}';

    axios.get('{{ route("admin.staff-attendance.members") }}', { params: { department: dept, date } })
        .then(res => {
            const { staff, already_marked, count } = res.data;

            document.getElementById('attendanceCard').style.display = 'none';
            document.getElementById('emptyState').style.display = 'none';

            if (!count) {
                document.getElementById('emptyState').style.display = '';
                return;
            }

            // Populate header labels
            document.getElementById('deptLabel').textContent = DEPT_LABELS[dept] ?? dept;
            document.getElementById('dateLabel').textContent = new Date(date).toLocaleDateString('en-PK', {day:'2-digit',month:'short',year:'numeric'});
            document.getElementById('alreadyMarkedBanner').style.display = already_marked ? '' : 'none';

            // Build attendanceData map
            attendanceData = {};
            staff.forEach(s => {
                attendanceData[s.id] = { status: s.status, remarks: s.remarks || '' };
            });

            renderRows(staff);
            updateCounters();
            document.getElementById('attendanceCard').style.display = '';
        })
        .catch(err => toastError(err.response?.data?.message || '{{ __("common.error") }}'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-users me-2"></i>{{ __("staff.load_staff") }}';
        });
}

// ── Render Rows ───────────────────────────────────────────────────────────
function renderRows(staff) {
    const list = document.getElementById('staffList');
    list.innerHTML = '';

    staff.forEach(s => {
        const row = document.createElement('div');
        row.className = 'staff-row';
        row.innerHTML = `
            <img src="${s.photo_url}" class="staff-avatar" alt="${s.name}">
            <div class="staff-info">
                <div class="staff-name">${s.name}</div>
                <div class="staff-desig">${s.designation}</div>
            </div>
            <div class="status-btns" id="btns-${s.id}">
                ${STATUSES.map(st => `
                    <button class="att-status-btn ${attendanceData[s.id].status === st ? 'active' : ''}"
                            data-status="${st}" data-id="${s.id}"
                            onclick="setStatus(${s.id}, '${st}')">
                        ${STATUS_LABELS[st]}
                    </button>`).join('')}
            </div>
            <input type="text" class="remarks-input" placeholder="{{ __('staff.col_remarks') }}..."
                   value="${s.remarks || ''}"
                   onchange="setRemarks(${s.id}, this.value)">
        `;
        list.appendChild(row);
    });
}

// ── Status Actions ────────────────────────────────────────────────────────
function setStatus(staffId, status) {
    attendanceData[staffId].status = status;
    // Update button states
    const btns = document.querySelectorAll(`#btns-${staffId} .att-status-btn`);
    btns.forEach(b => b.classList.toggle('active', b.dataset.status === status));
    updateCounters();
}

function setRemarks(staffId, remarks) {
    attendanceData[staffId].remarks = remarks;
}

function markAll(status) {
    Object.keys(attendanceData).forEach(id => setStatus(parseInt(id), status));
}

// ── Counters ──────────────────────────────────────────────────────────────
function updateCounters() {
    const counts = { Present: 0, Absent: 0, Late: 0, Half_Day: 0, Leave: 0 };
    Object.values(attendanceData).forEach(d => counts[d.status] = (counts[d.status] || 0) + 1);
    document.getElementById('cntPresent').textContent = counts.Present;
    document.getElementById('cntAbsent').textContent  = counts.Absent;
    document.getElementById('cntLate').textContent    = counts.Late;
    document.getElementById('cntHalf').textContent    = counts.Half_Day;
    document.getElementById('cntLeave').textContent   = counts.Leave;
}

// ── Save ──────────────────────────────────────────────────────────────────
function saveAttendance() {
    const dept = document.getElementById('deptSelect').value;
    const date = document.getElementById('dateSelect').value;

    if (!Object.keys(attendanceData).length) {
        toastError('{{ __("staff.no_staff_found") }}');
        return;
    }

    const attendance = Object.entries(attendanceData).map(([staff_id, d]) => ({
        staff_id: parseInt(staff_id),
        status:   d.status,
        remarks:  d.remarks || null,
    }));

    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __("common.loading") }}';

    axios.post('{{ route("admin.staff-attendance.store") }}', { department: dept, date, attendance })
        .then(res => {
            toastSuccess(res.data.message);
            document.getElementById('alreadyMarkedBanner').style.display = '';
        })
        .catch(err => toastError(err.response?.data?.message || '{{ __("common.error") }}'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>{{ __("common.save") }} {{ __("staff.attendance") }}';
        });
}
</script>
@endpush
