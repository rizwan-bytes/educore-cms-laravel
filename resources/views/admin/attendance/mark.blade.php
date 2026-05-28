@extends('layouts.app')
@section('title', __('attendance.mark'))

@section('content')

{{-- Page Header --}}
<div class="glass-card" style="margin-bottom:20px">
    <div class="card-header">
        <span>
            <i class="fas fa-calendar-check me-2" style="color:var(--green)"></i>
            {{ __('attendance.mark') }}
        </span>
        <a href="{{ route('admin.attendance.index') }}" class="btn-outline-custom" style="font-size:.83rem">
            <i class="fas fa-clock-rotate-left me-1"></i>
            {{ __('attendance.back_to_history') }}
        </a>
    </div>

    {{-- Class + Date Selector --}}
    <div style="padding:20px 24px;border-bottom:1px solid var(--border)">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label" style="color:var(--text-2);font-size:.85rem">
                    {{ __('attendance.filter_class') }} <span class="text-danger">*</span>
                </label>
                <select id="classSelect" class="form-select"
                        style="background:var(--surface);border-color:var(--border);color:var(--text)">
                    <option value="">— {{ __('attendance.select_class') }} —</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">
                            {{ $class->name }}{{ $class->section ? ' — '.$class->section : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="color:var(--text-2);font-size:.85rem">
                    {{ __('attendance.filter_date') }} <span class="text-danger">*</span>
                </label>
                <input type="date" id="dateSelect" class="form-control"
                       value="{{ date('Y-m-d') }}"
                       max="{{ date('Y-m-d') }}"
                       style="background:var(--surface);border-color:var(--border);color:var(--text)">
            </div>
            <div class="col-md-3">
                <button id="loadBtn" class="btn-primary-custom w-100" onclick="loadStudents()" style="height:42px">
                    <i class="fas fa-users me-1"></i>
                    {{ __('attendance.load_students') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Status Bar (hidden until loaded) --}}
    <div id="statusBar" style="display:none;padding:12px 24px;background:rgba(16,185,129,.06);border-bottom:1px solid var(--border)">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div style="color:var(--text-2);font-size:.84rem">
                <span id="studentCountBadge"></span>
                <span id="alreadyMarkedBadge" style="display:none;margin-left:10px;background:rgba(245,158,11,.15);color:#f59e0b;padding:2px 10px;border-radius:20px;font-size:.75rem;font-weight:500">
                    <i class="fas fa-pen me-1"></i>{{ __('attendance.already_marked') }}
                </span>
            </div>
            {{-- Quick mark all --}}
            <div class="d-flex gap-2">
                <button type="button" onclick="markAll('Present')" class="btn-att-quick present">
                    <i class="fas fa-circle-check me-1"></i>{{ __('attendance.mark_all_present') }}
                </button>
                <button type="button" onclick="markAll('Absent')" class="btn-att-quick absent">
                    <i class="fas fa-circle-xmark me-1"></i>{{ __('attendance.mark_all_absent') }}
                </button>
                <button type="button" onclick="markAll('Late')" class="btn-att-quick late">
                    <i class="fas fa-clock me-1"></i>{{ __('attendance.mark_all_late') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Student Grid --}}
    <div id="studentGrid" style="padding:20px 24px;display:none">
        <div id="noStudentsMsg" style="display:none;text-align:center;padding:40px;color:var(--muted)">
            <i class="fas fa-user-slash fa-2x mb-2" style="display:block"></i>
            {{ __('attendance.no_students') }}
        </div>
        <div id="studentsContainer"></div>
    </div>

    {{-- Submit Footer (hidden until loaded) --}}
    <div id="submitBar" style="display:none;padding:16px 24px;border-top:1px solid var(--border);background:var(--surface)">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            {{-- Live counters --}}
            <div class="d-flex gap-3" id="liveCounters">
                <span class="att-counter present">
                    <i class="fas fa-circle-check"></i>
                    <span id="countPresent">0</span> {{ __('attendance.present') }}
                </span>
                <span class="att-counter absent">
                    <i class="fas fa-circle-xmark"></i>
                    <span id="countAbsent">0</span> {{ __('attendance.absent') }}
                </span>
                <span class="att-counter late">
                    <i class="fas fa-clock"></i>
                    <span id="countLate">0</span> {{ __('attendance.late') }}
                </span>
            </div>
            <button id="saveBtn" onclick="saveAttendance()" class="btn-primary-custom" style="min-width:180px">
                <i class="fas fa-floppy-disk me-1"></i>
                {{ __('attendance.submit') }}
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ──────────────────────────────────────────────────────────────────────────
// MARK ATTENDANCE — Class + Date → Load Students → P/A/L → Save
// ──────────────────────────────────────────────────────────────────────────

var studentsData = [];   // loaded students array

// ── Load Students ─────────────────────────────────────────────────────────
function loadStudents() {
    var classId = document.getElementById('classSelect').value;
    var date    = document.getElementById('dateSelect').value;

    if (!classId || !date) {
        toastWarning('{{ __("attendance.select_class") }} {{ __("common.and") }} {{ __("attendance.select_date") }}');
        return;
    }

    var btn = document.getElementById('loadBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading...';

    axios.get('{{ route("admin.attendance.students") }}', { params: { class_id: classId, date: date } })
        .then(function (res) {
            studentsData = res.data.students;
            renderStudents(studentsData, res.data.already_marked);
        })
        .catch(function () {
            toastError('{{ __("common.error") }}');
        })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-users me-1"></i> {{ __("attendance.load_students") }}';
        });
}

// ── Render Student Grid ───────────────────────────────────────────────────
function renderStudents(students, alreadyMarked) {
    var container = document.getElementById('studentsContainer');
    var noMsg     = document.getElementById('noStudentsMsg');

    document.getElementById('statusBar').style.display = '';
    document.getElementById('studentGrid').style.display = '';
    document.getElementById('submitBar').style.display = students.length ? '' : 'none';

    // already-marked badge
    document.getElementById('alreadyMarkedBadge').style.display = alreadyMarked ? '' : 'none';
    document.getElementById('studentCountBadge').innerHTML =
        '<strong style="color:var(--text)">' + students.length + '</strong> {{ __("attendance.student_name") }}';

    if (!students.length) {
        noMsg.style.display      = '';
        container.style.display  = 'none';
        return;
    }

    noMsg.style.display      = 'none';
    container.style.display  = '';

    var html = '<div class="att-grid">';
    students.forEach(function (s, i) {
        var initial  = s.name.charAt(0).toUpperCase();
        var isFemale = s.gender === 'Female';
        var avatarBg = isFemale
            ? 'linear-gradient(135deg,#ec4899,#db2777)'
            : 'linear-gradient(135deg,#6366f1,#8b5cf6)';

        html += '<div class="att-row" data-index="' + i + '" id="attRow' + s.id + '">'
            + '<div class="att-avatar" style="background:' + avatarBg + '">' + initial + '</div>'
            + '<div class="att-info">'
            +   '<div class="att-name">' + escHtml(s.name) + '</div>'
            +   '<div class="att-roll">' + escHtml(s.roll) + '</div>'
            + '</div>'
            + '<div class="att-btns">'
            +   '<button type="button" class="btn-att present ' + (s.status === 'Present' ? 'active' : '') + '" onclick="setStatus(' + s.id + ',\'Present\')">'
            +     '<i class="fas fa-circle-check"></i> {{ __("attendance.present") }}'
            +   '</button>'
            +   '<button type="button" class="btn-att absent ' + (s.status === 'Absent' ? 'active' : '') + '" onclick="setStatus(' + s.id + ',\'Absent\')">'
            +     '<i class="fas fa-circle-xmark"></i> {{ __("attendance.absent") }}'
            +   '</button>'
            +   '<button type="button" class="btn-att late ' + (s.status === 'Late' ? 'active' : '') + '" onclick="setStatus(' + s.id + ',\'Late\')">'
            +     '<i class="fas fa-clock"></i> {{ __("attendance.late") }}'
            +   '</button>'
            + '</div>'
            + '</div>';
    });
    html += '</div>';

    container.innerHTML = html;
    updateCounters();
}

// ── Set Status for one student ────────────────────────────────────────────
function setStatus(studentId, status) {
    var student = studentsData.find(function(s) { return s.id === studentId; });
    if (student) student.status = status;

    // Update button active states
    var row  = document.getElementById('attRow' + studentId);
    if (!row) return;
    row.querySelectorAll('.btn-att').forEach(function (btn) {
        btn.classList.remove('active');
    });
    var target = row.querySelector('.btn-att.' + status.toLowerCase());
    if (target) target.classList.add('active');

    updateCounters();
}

// ── Mark All ──────────────────────────────────────────────────────────────
function markAll(status) {
    studentsData.forEach(function (s) { s.status = status; });
    // Re-render buttons only
    studentsData.forEach(function (s) {
        var row = document.getElementById('attRow' + s.id);
        if (!row) return;
        row.querySelectorAll('.btn-att').forEach(function (btn) {
            btn.classList.remove('active');
        });
        var target = row.querySelector('.btn-att.' + status.toLowerCase());
        if (target) target.classList.add('active');
    });
    updateCounters();
}

// ── Live Counters ─────────────────────────────────────────────────────────
function updateCounters() {
    var p = 0, a = 0, l = 0;
    studentsData.forEach(function (s) {
        if (s.status === 'Present') p++;
        else if (s.status === 'Absent') a++;
        else if (s.status === 'Late') l++;
    });
    document.getElementById('countPresent').textContent = p;
    document.getElementById('countAbsent').textContent  = a;
    document.getElementById('countLate').textContent    = l;
}

// ── Save Attendance ───────────────────────────────────────────────────────
function saveAttendance() {
    if (!studentsData.length) return;

    var classId = document.getElementById('classSelect').value;
    var date    = document.getElementById('dateSelect').value;
    var btn     = document.getElementById('saveBtn');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

    var payload = {
        class_id:   classId,
        date:       date,
        attendance: studentsData.map(function (s) {
            return { student_id: s.id, status: s.status };
        })
    };

    axios.post('{{ route("admin.attendance.store") }}', payload)
        .then(function (res) {
            toastSuccess(res.data.message);
            // Mark "already marked" badge
            document.getElementById('alreadyMarkedBadge').style.display = '';
        })
        .catch(function (err) {
            toastError(err.response?.data?.message || '{{ __("common.error") }}');
        })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-floppy-disk me-1"></i> {{ __("attendance.submit") }}';
        });
}

// ── HTML escape helper ────────────────────────────────────────────────────
function escHtml(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}

// Auto-load if today's date is set
window.addEventListener('DOMContentLoaded', function () {
    // Allow pressing Enter on date/class to load
    ['classSelect','dateSelect'].forEach(function (id) {
        document.getElementById(id).addEventListener('keydown', function (e) {
            if (e.key === 'Enter') loadStudents();
        });
    });
});
</script>
@endpush

@push('styles')
<style>
/* ── Attendance Mark Page ─────────────────────────────────────────── */

/* Student grid */
.att-grid {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.att-row {
    display: flex;
    align-items: center;
    gap: 14px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 16px;
    transition: border-color .15s;
}
.att-row:hover { border-color: rgba(99,102,241,.25); }

.att-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .95rem; color: #fff;
    flex-shrink: 0;
}

.att-info { flex: 1; min-width: 0; }
.att-name { font-weight: 500; color: var(--text); font-size: .9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.att-roll { font-size: .75rem; color: var(--muted); margin-top: 1px; }

/* P / A / L buttons */
.att-btns { display: flex; gap: 6px; flex-shrink: 0; }

.btn-att {
    border: 1px solid var(--border);
    background: var(--card);
    color: var(--text-2);
    border-radius: 8px;
    padding: 5px 12px;
    font-size: .78rem;
    font-weight: 500;
    cursor: pointer;
    transition: all .15s;
    display: flex; align-items: center; gap: 5px;
    white-space: nowrap;
}
.btn-att:hover { opacity: .85; }

/* Active states */
.btn-att.present.active { background: rgba(16,185,129,.18); border-color: #10b981; color: #10b981; }
.btn-att.absent.active  { background: rgba(239,68,68,.15);  border-color: #ef4444; color: #ef4444; }
.btn-att.late.active    { background: rgba(245,158,11,.15); border-color: #f59e0b; color: #f59e0b; }

/* Quick mark buttons */
.btn-att-quick {
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-2);
    border-radius: 8px;
    padding: 5px 14px;
    font-size: .78rem;
    font-weight: 500;
    cursor: pointer;
    transition: all .15s;
}
.btn-att-quick.present:hover { background: rgba(16,185,129,.15); border-color: #10b981; color: #10b981; }
.btn-att-quick.absent:hover  { background: rgba(239,68,68,.12);  border-color: #ef4444; color: #ef4444; }
.btn-att-quick.late:hover    { background: rgba(245,158,11,.12); border-color: #f59e0b; color: #f59e0b; }

/* Live counters */
.att-counter {
    display: flex; align-items: center; gap: 6px;
    font-size: .82rem; font-weight: 600; padding: 5px 14px;
    border-radius: 20px;
}
.att-counter.present { background: rgba(16,185,129,.12); color: #10b981; }
.att-counter.absent  { background: rgba(239,68,68,.12);  color: #ef4444; }
.att-counter.late    { background: rgba(245,158,11,.12); color: #f59e0b; }

/* Responsive */
@media (max-width: 576px) {
    .att-row { flex-wrap: wrap; }
    .att-btns { width: 100%; justify-content: flex-end; }
    .btn-att { padding: 5px 10px; font-size: .72rem; }
}
</style>
@endpush
