<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // Helper: get logged-in teacher model
    private function teacher()
    {
        return Auth::user()->teacher;
    }

    // Mark attendance page — only assigned classes
    public function index()
    {
        $teacher = $this->teacher();

        if (!$teacher) {
            return view('teacher.attendance', ['classes' => collect(), 'noProfile' => true]);
        }

        // Only classes where this teacher has at least 1 subject assigned
        $classes = $teacher->assignedClasses();

        return view('teacher.attendance', compact('classes'));
    }

    // Ajax: students for class+date — verify class belongs to teacher
    public function students(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date'     => 'required|date',
        ]);

        $teacher = $this->teacher();

        // Security: verify teacher is assigned to at least 1 subject in this class
        if ($teacher) {
            $allowed = $teacher->subjects()
                ->where('class_id', $request->class_id)
                ->exists();

            if (!$allowed) {
                return response()->json(['message' => __('common.unauthorized')], 403);
            }
        }

        $students = Student::with('user')
            ->where('class_id', $request->class_id)
            ->where('status', true)
            ->orderBy('roll_no')
            ->get();

        $existing = Attendance::where('class_id', $request->class_id)
            ->whereDate('date', $request->date)
            ->get()
            ->keyBy('student_id');

        $data = $students->map(fn($s) => [
            'id'     => $s->id,
            'name'   => $s->user->name ?? '—',
            'roll'   => $s->roll_no    ?? '—',
            'gender' => $s->gender     ?? 'Male',
            'status' => $existing->has($s->id) ? $existing[$s->id]->status : 'Present',
        ]);

        return response()->json([
            'students'       => $data,
            'already_marked' => $existing->isNotEmpty(),
            'count'          => $students->count(),
        ]);
    }

    // Store bulk attendance — verify class belongs to teacher
    public function store(Request $request)
    {
        $request->validate([
            'class_id'                => 'required|exists:classes,id',
            'date'                    => 'required|date',
            'attendance'              => 'required|array|min:1',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status'     => 'required|in:Present,Absent,Late',
        ]);

        $teacher = $this->teacher();

        // Security check
        if ($teacher) {
            $allowed = $teacher->subjects()
                ->where('class_id', $request->class_id)
                ->exists();

            if (!$allowed) {
                return response()->json(['message' => __('common.unauthorized')], 403);
            }
        }

        foreach ($request->attendance as $row) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $row['student_id'],
                    'class_id'   => $request->class_id,
                    'date'       => $request->date,
                ],
                [
                    'status'    => $row['status'],
                    'marked_by' => Auth::id(),
                ]
            );
        }

        return response()->json(['success' => true, 'message' => __('attendance.saved_success')]);
    }
}
