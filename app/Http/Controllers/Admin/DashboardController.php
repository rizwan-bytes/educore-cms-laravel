<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Fee;
use App\Models\Notice;
use App\Models\ClassRoom;
use App\Models\Attendance;
use App\Models\Result;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stats ─────────────────────────────────────────────────────
        $stats = [
            'students'     => Student::where('status', true)->count(),
            'teachers'     => Teacher::where('status', true)->count(),
            'classes'      => ClassRoom::count(),
            'subjects'     => DB::table('subjects')->count(),
            'fees_paid'    => (float) Fee::where('status', 'Paid')->sum('amount'),
            'fees_pending' => (float) Fee::where('status', 'Pending')->sum('amount'),
            'fees_overdue' => (float) Fee::where('status', 'Overdue')->sum('amount'),
            'notices'      => Notice::where('status', true)->count(),
        ];

        // ── Chart 1: 7-day attendance trend ───────────────────────────
        $attData = DB::table('attendance')
            ->select(
                'date',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present"),
                DB::raw("SUM(CASE WHEN status='Absent'  THEN 1 ELSE 0 END) as absent")
            )
            ->where('date', '>=', now()->subDays(7)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Chart 2: Fee status doughnut ──────────────────────────────
        $feeChart = [
            'paid'    => $stats['fees_paid'],
            'pending' => $stats['fees_pending'],
            'overdue' => $stats['fees_overdue'],
        ];

        // ── Chart 3: Students per class ───────────────────────────────
        $classData = DB::table('classes as c')
            ->select(
                DB::raw("CONCAT(c.name, IF(c.section IS NOT NULL AND c.section != '', CONCAT(' ', c.section), '')) as label"),
                DB::raw('COUNT(s.id) as count')
            )
            ->leftJoin('students as s', function ($join) {
                $join->on('s.class_id', '=', 'c.id')->where('s.status', true);
            })
            ->groupBy('c.id', 'c.name', 'c.section')
            ->orderByDesc('count')
            ->get();

        // ── Chart 4: Grade distribution ───────────────────────────────
        $gradeData = DB::table('results')
            ->select('grade', DB::raw('COUNT(*) as count'))
            ->whereNotNull('grade')
            ->where('grade', '!=', '')
            ->groupBy('grade')
            ->orderByRaw("FIELD(grade,'A+','A','B','C','D','F')")
            ->get();

        // ── Chart 5: Monthly fee collection (last 6 months) ───────────
        $monthlyFees = DB::table('fees')
            ->select(
                DB::raw("DATE_FORMAT(paid_date, '%b %Y') as month"),
                DB::raw('COALESCE(SUM(amount), 0) as total')
            )
            ->where('status', 'Paid')
            ->whereNotNull('paid_date')
            ->where('paid_date', '>=', now()->subMonths(6)->toDateString())
            ->groupBy(DB::raw("DATE_FORMAT(paid_date, '%Y-%m')"), DB::raw("DATE_FORMAT(paid_date, '%b %Y')"))
            ->orderBy(DB::raw("MIN(paid_date)"))
            ->get();

        // ── Overall attendance % ──────────────────────────────────────
        $totalAtt   = Attendance::count();
        $presentAtt = Attendance::where('status', 'Present')->count();
        $attOverall = $totalAtt > 0 ? round($presentAtt / $totalAtt * 100) : 0;

        // ── Recent students ───────────────────────────────────────────
        $recentStudents = Student::with(['user', 'class'])
            ->latest()
            ->limit(6)
            ->get();

        // ── Latest notices ────────────────────────────────────────────
        $notices = Notice::with('author')
            ->where('status', true)
            ->latest()
            ->limit(4)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'attData',
            'feeChart',
            'classData',
            'gradeData',
            'monthlyFees',
            'attOverall',
            'recentStudents',
            'notices'
        ));
    }
}
