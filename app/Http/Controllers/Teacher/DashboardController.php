<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Notice;
use App\Models\Student;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stats ────────────────────────────────────────────────────────────
        $totalStudents = Student::where('status', true)->count();
        $totalClasses  = ClassRoom::where('status', true)->count();

        // Today's overall attendance %
        $today          = Carbon::today()->toDateString();
        $todayTotal     = Attendance::whereDate('date', $today)->count();
        $todayPresent   = Attendance::whereDate('date', $today)->where('status', 'Present')->count();
        $todayAttPct    = $todayTotal > 0 ? round(($todayPresent / $todayTotal) * 100) : null;

        // Active notices for teacher/all
        $noticesCount = Notice::where('status', true)
            ->whereIn('target_role', ['teacher', 'all'])
            ->count();

        // Recent 5 notices
        $recentNotices = Notice::where('status', true)
            ->whereIn('target_role', ['teacher', 'all'])
            ->latest()
            ->limit(5)
            ->get();

        // Attendance trend — last 7 days (% per day)
        $trendLabels = [];
        $trendData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $d     = Carbon::today()->subDays($i);
            $label = $d->format('d M');
            $total = Attendance::whereDate('date', $d)->count();
            $pres  = Attendance::whereDate('date', $d)->where('status', 'Present')->count();
            $trendLabels[] = $label;
            $trendData[]   = $total > 0 ? round(($pres / $total) * 100) : 0;
        }

        // Status breakdown today
        $todayAbsent = Attendance::whereDate('date', $today)->where('status', 'Absent')->count();
        $todayLate   = Attendance::whereDate('date', $today)->where('status', 'Late')->count();

        return view('teacher.dashboard', compact(
            'totalStudents', 'totalClasses',
            'todayAttPct', 'todayPresent', 'todayTotal', 'todayAbsent', 'todayLate',
            'noticesCount', 'recentNotices',
            'trendLabels', 'trendData'
        ));
    }
}
