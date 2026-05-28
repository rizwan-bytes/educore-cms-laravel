<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // ── Helper ────────────────────────────────────────────────────────────
    private function teacher()
    {
        return Auth::user()->teacher;
    }

    // ── Mark Attendance Page ──────────────────────────────────────────────
    public function index()
    {
        $teacher = $this->teacher();

        if (!$teacher) {
            return view('teacher.attendance', ['classes' => collect(), 'noProfile' => true]);
        }

        $classes = $teacher->assignedClasses();

        return view('teacher.attendance', compact('classes', 'teacher'));
    }

    // ── Ajax: Students for Class + Date (+ Subject for subject_wise) ──────
    public function students(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'date'       => 'required|date',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $teacher = $this->teacher();
        if (!$teacher) {
            return response()->json(['message' => __('common.unauthorized')], 403);
        }

        $class = ClassRoom::findOrFail($request->class_id);

        // ── Security: verify teacher can mark this class ──────────────────
        if (!$teacher->canMarkAttendance($class)) {
            return response()->json(['message' => __('common.unauthorized')], 403);
        }

        // ── For subject_wise: need subject_id ────────────────────────────
        if ($class->isSubjectWise()) {
            // Return subjects list if no subject selected yet
            if (!$request->filled('subject_id')) {
                $subjects = $teacher->subjectsForClass($class->id);
                return response()->json([
                    'mode'           => 'subject_wise',
                    'subjects'       => $subjects->map(fn($s) => ['id' => $s->id, 'name' => $s->name]),
                    'students'       => [],
                    'already_marked' => false,
                ]);
            }

            // Verify teacher owns this subject
            $subjectAllowed = $teacher->subjects()
                ->where('id', $request->subject_id)
                ->where('class_id', $request->class_id)
                ->exists();

            if (!$subjectAllowed) {
                return response()->json(['message' => __('common.unauthorized')], 403);
            }
        }

        $students = Student::with('user')
            ->where('class_id', $request->class_id)
            ->where('status', true)
            ->orderBy('roll_no')
            ->get();

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
                                    ? $teacher->subjectsForClass($class->id)->map(fn($s) => ['id' => $s->id, 'name' => $s->name])
                                    : [],
            'students'       => $data,
            'already_marked' => $existing->isNotEmpty(),
            'count'          => $students->count(),
        ]);
    }

    // ── Store Attendance ──────────────────────────────────────────────────
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

        $teacher = $this->teacher();
        if (!$teacher) {
            return response()->json(['message' => __('common.unauthorized')], 403);
        }

        $class = ClassRoom::findOrFail($request->class_id);

        // Security check
        if (!$teacher->canMarkAttendance($class)) {
            return response()->json(['message' => __('common.unauthorized')], 403);
        }

        // For subject_wise: verify subject belongs to teacher
        if ($class->isSubjectWise()) {
            if (!$request->filled('subject_id')) {
                return response()->json(['message' => __('attendance.select_subject')], 422);
            }
            $subjectAllowed = $teacher->subjects()
                ->where('id', $request->subject_id)
                ->where('class_id', $request->class_id)
                ->exists();
            if (!$subjectAllowed) {
                return response()->json(['message' => __('common.unauthorized')], 403);
            }
        }

        $subjectId = $class->isSubjectWise() ? $request->subject_id : null;

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
                    'marked_by' => Auth::id(),
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => __('attendance.saved_success'),
        ]);
    }
}
