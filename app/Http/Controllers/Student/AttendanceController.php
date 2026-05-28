<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return view('student.attendance', ['student' => null, 'stats' => []]);
        }

        // Overall stats
        $total   = Attendance::where('student_id', $student->id)->count();
        $present = Attendance::where('student_id', $student->id)->where('status', 'Present')->count();
        $absent  = Attendance::where('student_id', $student->id)->where('status', 'Absent')->count();
        $late    = Attendance::where('student_id', $student->id)->where('status', 'Late')->count();
        $pct     = $total > 0 ? round(($present / $total) * 100) : 0;

        // Monthly breakdown — last 6 months
        $monthlyLabels  = [];
        $monthlyPresent = [];
        $monthlyAbsent  = [];
        for ($i = 5; $i >= 0; $i--) {
            $m      = Carbon::now()->subMonths($i);
            $mStart = $m->copy()->startOfMonth();
            $mEnd   = $m->copy()->endOfMonth();
            $mQuery = Attendance::where('student_id', $student->id)
                ->whereBetween('date', [$mStart, $mEnd]);
            $monthlyLabels[]  = $m->format('M Y');
            $monthlyPresent[] = (clone $mQuery)->where('status', 'Present')->count();
            $monthlyAbsent[]  = (clone $mQuery)->where('status', 'Absent')->count();
        }

        $stats = compact('total', 'present', 'absent', 'late', 'pct');

        return view('student.attendance', compact(
            'student', 'stats',
            'monthlyLabels', 'monthlyPresent', 'monthlyAbsent'
        ));
    }

    // Ajax DataTable
    public function data(Request $request)
    {
        $student = auth()->user()->student;
        if (!$student) {
            return response()->json(['data' => []]);
        }

        $query = Attendance::where('student_id', $student->id)->select('attendance.*');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('month')) {
            $query->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$request->month]);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('date_fmt', fn($a) => $a->date ? $a->date->format('d M Y') : '—')
            ->addColumn('day_name', fn($a) => $a->date ? $a->date->format('l') : '—')
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
            ->rawColumns(['status_badge'])
            ->make(true);
    }
}
