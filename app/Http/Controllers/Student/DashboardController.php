<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Notice;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $student = $user->student;

        if (!$student) {
            return view('student.dashboard', ['student' => null]);
        }

        $class = $student->class;

        // ── Attendance Stats ─────────────────────────────────────────────────
        $allAtt     = Attendance::where('student_id', $student->id);
        $totalDays  = (clone $allAtt)->count();
        $presentDays= (clone $allAtt)->where('status', 'Present')->count();
        $absentDays = (clone $allAtt)->where('status', 'Absent')->count();
        $lateDays   = (clone $allAtt)->where('status', 'Late')->count();
        $attPct     = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        // This month
        $monthStart   = Carbon::now()->startOfMonth();
        $monthAtt     = Attendance::where('student_id', $student->id)->where('date', '>=', $monthStart);
        $monthTotal   = (clone $monthAtt)->count();
        $monthPresent = (clone $monthAtt)->where('status', 'Present')->count();
        $monthPct     = $monthTotal > 0 ? round(($monthPresent / $monthTotal) * 100) : 0;

        // Last 7 days attendance for mini chart
        $chartLabels = [];
        $chartData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $rec = Attendance::where('student_id', $student->id)
                ->whereDate('date', $d)->first();
            $chartLabels[] = $d->format('d M');
            $chartData[]   = $rec ? ($rec->status === 'Present' ? 1 : ($rec->status === 'Late' ? 0.5 : 0)) : null;
        }

        // Recent 3 attendance records
        $recentAtt = Attendance::where('student_id', $student->id)
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        // Active notices for student/all
        $notices = Notice::where('status', true)
            ->whereIn('target_role', ['student', 'all'])
            ->latest()
            ->limit(5)
            ->get();

        return view('student.dashboard', compact(
            'student', 'class', 'user',
            'totalDays', 'presentDays', 'absentDays', 'lateDays', 'attPct',
            'monthTotal', 'monthPresent', 'monthPct',
            'chartLabels', 'chartData',
            'recentAtt', 'notices'
        ));
    }
}
