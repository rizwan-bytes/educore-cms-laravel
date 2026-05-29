<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StaffAttendanceController extends Controller
{
    // ── History Index ────────────────────────────────────────────────────
    public function index()
    {
        abort_unless(TenantService::canUse('staff_management'), 403,
            __('staff.feature_unavailable'));

        $departments = Staff::DEPARTMENTS;

        return view('admin.staff-attendance.index', compact('departments'));
    }

    // ── DataTable Data ───────────────────────────────────────────────────
    public function data(Request $request)
    {
        abort_unless(TenantService::canUse('staff_management'), 403);

        $query = StaffAttendance::with(['staff', 'marker'])
            ->select('staff_attendance.*');

        if ($request->filled('department')) {
            $query->whereHas('staff', fn($q) => $q->where('department', $request->department));
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('staff_info', function (StaffAttendance $a) {
                $s = $a->staff;
                return '<div class="dt-name-cell">
                    <img src="' . e($s?->photo_url ?? '') . '" class="dt-avatar" alt="">
                    <div>
                        <div class="dt-name">' . e($s?->name ?? '—') . '</div>
                        <div class="dt-sub">' . e($s?->designation ?? '—') . '</div>
                    </div>
                </div>';
            })
            ->addColumn('dept_badge', function (StaffAttendance $a) {
                $s = $a->staff;
                if (!$s) return '—';
                $colors = [
                    'administrative'   => 'var(--primary-lt)',
                    'finance'          => 'var(--green)',
                    'academic_support' => 'var(--cyan)',
                    'support'          => 'var(--orange)',
                ];
                $color = $colors[$s->department] ?? 'var(--text-2)';
                return '<span style="background:' . $color . '20;color:' . $color . ';border:1px solid ' . $color . '40;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">'
                     . e($s->department_label) . '</span>';
            })
            ->addColumn('date_fmt', fn($a) => $a->date->format('d M Y'))
            ->addColumn('status_badge', function (StaffAttendance $a) {
                $colors = [
                    'Present'  => 'var(--green)',
                    'Absent'   => 'var(--red)',
                    'Late'     => 'var(--yellow)',
                    'Half_Day' => 'var(--orange)',
                    'Leave'    => 'var(--cyan)',
                ];
                $color = $colors[$a->status] ?? 'var(--muted)';
                $label = $a->status === 'Half_Day' ? __('staff.status_half_day') : __('staff.status_' . strtolower($a->status));
                return '<span style="background:' . $color . '20;color:' . $color . ';border:1px solid ' . $color . '40;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">'
                     . $label . '</span>';
            })
            ->addColumn('marked_by_name', fn($a) => $a->marker?->name ?? '—')
            ->rawColumns(['staff_info', 'dept_badge', 'status_badge'])
            ->make(true);
    }

    // ── Mark Page ────────────────────────────────────────────────────────
    public function mark()
    {
        abort_unless(TenantService::canUse('staff_management'), 403);

        $departments = Staff::DEPARTMENTS;

        return view('admin.staff-attendance.mark', compact('departments'));
    }

    // ── Ajax: Load Staff Members for Dept + Date ──────────────────────────
    public function members(Request $request)
    {
        abort_unless(TenantService::canUse('staff_management'), 403);

        $request->validate([
            'department' => 'required|in:administrative,finance,academic_support,support',
            'date'       => 'required|date',
        ]);

        $staff = Staff::where('department', $request->department)
            ->where('status', true)
            ->orderBy('name')
            ->get();

        $existing = StaffAttendance::where('date', $request->date)
            ->whereIn('staff_id', $staff->pluck('id'))
            ->get()
            ->keyBy('staff_id');

        $data = $staff->map(fn($s) => [
            'id'          => $s->id,
            'name'        => $s->name,
            'designation' => $s->designation ?? '—',
            'photo_url'   => $s->photo_url,
            'status'      => $existing->has($s->id) ? $existing[$s->id]->status : 'Present',
            'remarks'     => $existing->has($s->id) ? ($existing[$s->id]->remarks ?? '') : '',
        ]);

        return response()->json([
            'staff'          => $data,
            'already_marked' => $existing->isNotEmpty(),
            'count'          => $staff->count(),
        ]);
    }

    // ── Store ────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        abort_unless(TenantService::canUse('staff_management'), 403);

        $request->validate([
            'department'          => 'required|in:administrative,finance,academic_support,support',
            'date'                => 'required|date',
            'attendance'          => 'required|array|min:1',
            'attendance.*.staff_id' => 'required|exists:staff,id',
            'attendance.*.status' => 'required|in:Present,Absent,Late,Half_Day,Leave',
            'attendance.*.remarks'=> 'nullable|string|max:255',
        ]);

        foreach ($request->attendance as $row) {
            StaffAttendance::updateOrCreate(
                [
                    'staff_id' => $row['staff_id'],
                    'date'     => $request->date,
                ],
                [
                    'status'    => $row['status'],
                    'remarks'   => $row['remarks'] ?? null,
                    'marked_by' => Auth::id(),
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => __('staff.attendance_saved'),
        ]);
    }
}
