<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    // ── History Page ─────────────────────────────────────────────────────────
    public function index()
    {
        $classes  = ClassRoom::where('status', true)->orderBy('name')->get();
        $subjects = Subject::where('status', true)->orderBy('name')->get();
        return view('admin.attendance.index', compact('classes', 'subjects'));
    }

    // ── DataTable Server-Side ────────────────────────────────────────────────
    public function data(Request $request)
    {
        $query = Attendance::with(['student.user', 'student.class', 'subject'])
            ->select('attendance.*');

        if ($request->filled('class_id')) {
            $query->where('attendance.class_id', $request->class_id);
        }
        if ($request->filled('status')) {
            $query->where('attendance.status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('attendance.date', $request->date);
        }
        if ($request->filled('subject_id')) {
            $query->where('attendance.subject_id', $request->subject_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('student_name', function ($a) {
                $name = $a->student->user->name ?? '—';
                $roll = $a->student->roll_no     ?? '';
                return '<div>
                            <div style="font-weight:500;color:var(--text)">' . e($name) . '</div>
                            <div class="dt-sub">' . e($roll) . '</div>
                        </div>';
            })
            ->addColumn('class_name', function ($a) {
                $cls = $a->student->class ?? null;
                return $cls
                    ? e($cls->name . ($cls->section ? ' — ' . $cls->section : ''))
                    : '—';
            })
            ->addColumn('subject_name', function ($a) {
                if ($a->subject) {
                    return '<span style="color:var(--yellow);font-size:.82rem">'
                        . e($a->subject->name) . '</span>';
                }
                return '<span style="color:var(--muted);font-size:.8rem">'
                    . __('classes.mode_class_incharge') . '</span>';
            })
            ->addColumn('date_fmt', function ($a) {
                return $a->date ? $a->date->format('d M Y') : '—';
            })
            ->addColumn('status_badge', function ($a) {
                $map = [
                    'Present' => ['#10b981', 'rgba(16,185,129,.12)'],
                    'Absent'  => ['#ef4444', 'rgba(239,68,68,.12)'],
                    'Late'    => ['#f59e0b', 'rgba(245,158,11,.12)'],
                ];
                [$color, $bg] = $map[$a->status] ?? ['var(--text-2)', 'var(--surface)'];
                $label = __('attendance.' . strtolower($a->status));
                return '<span style="background:' . $bg . ';color:' . $color
                    . ';padding:3px 12px;border-radius:20px;font-size:.72rem;font-weight:500">'
                    . e($label) . '</span>';
            })
            ->rawColumns(['student_name', 'subject_name', 'status_badge'])
            ->make(true);
    }

    // ── Mark Attendance Page ─────────────────────────────────────────────────
    public function mark()
    {
        $classes = ClassRoom::with('inchargeTeacher.user')
            ->where('status', true)
            ->orderBy('name')
            ->get();
        return view('admin.attendance.mark', compact('classes'));
    }

    // ── Ajax: Get Class Info + Students ──────────────────────────────────────
    public function students(Request $request)
    {
        $request->validate([
            'class_id'  => 'required|exists:classes,id',
            'date'      => 'required|date',
            'subject_id'=> 'nullable|exists:subjects,id',
        ]);

        $class = ClassRoom::with('subjects')->findOrFail($request->class_id);

        // For subject-wise mode, subject_id is required
        if ($class->isSubjectWise() && !$request->filled('subject_id')) {
            return response()->json([
                'mode'     => 'subject_wise',
                'subjects' => $class->subjects()->where('status', true)->get(['id', 'name']),
                'students' => [],
                'already_marked' => false,
            ]);
        }

        $students = Student::with('user')
            ->where('class_id', $request->class_id)
            ->where('status', true)
            ->orderBy('roll_no')
            ->get();

        // Build unique key for existing records
        $existingQuery = Attendance::where('class_id', $request->class_id)
            ->whereDate('date', $request->date);

        if ($class->isSubjectWise()) {
            $existingQuery->where('subject_id', $request->subject_id);
        } else {
            $existingQuery->whereNull('subject_id');
        }

        $existing = $existingQuery->get()->keyBy('student_id');

        $data = $students->map(fn($s) => [
            'id'     => $s->id,
            'name'   => $s->user->name ?? '—',
            'roll'   => $s->roll_no    ?? '—',
            'gender' => $s->gender     ?? 'Male',
            'status' => $existing->has($s->id) ? $existing[$s->id]->status : 'Present',
        ]);

        return response()->json([
            'mode'           => $class->attendance_mode,
            'subjects'       => $class->isSubjectWise()
                                    ? $class->subjects()->where('status', true)->get(['id', 'name'])
                                    : [],
            'students'       => $data,
            'already_marked' => $existing->isNotEmpty(),
            'count'          => $students->count(),
        ]);
    }

    // ── Store / Update Bulk Attendance ───────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'class_id'                => 'required|exists:classes,id',
            'date'                    => 'required|date',
            'subject_id'              => 'nullable|exists:subjects,id',
            'attendance'              => 'required|array|min:1',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status'     => 'required|in:Present,Absent,Late',
        ]);

        $class  = ClassRoom::findOrFail($request->class_id);
        $userId = Auth::id();

        // Unique key: class + date + subject (null for class_incharge)
        $subjectId = $class->isSubjectWise() ? ($request->subject_id ?: null) : null;

        foreach ($request->attendance as $row) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $row['student_id'],
                    'class_id'   => $request->class_id,
                    'subject_id' => $subjectId,
                    'date'       => $request->date,
                ],
                [
                    'status'    => $row['status'],
                    'marked_by' => $userId,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => __('attendance.saved_success'),
        ]);
    }
}
