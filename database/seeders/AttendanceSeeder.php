<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding attendance data (30 working days)...');

        $adminUser   = User::where('role', 'admin')->first();
        $adminId     = $adminUser?->id ?? 1;
        $allStudents = Student::where('status', true)->get();

        if ($allStudents->isEmpty()) {
            $this->command->warn('  ⚠ No students found. Run SampleDataSeeder first.');
            return;
        }

        // Build list of 30 past working days (Mon–Fri only)
        $workDays = collect();
        $cursor   = Carbon::today()->subDays(1);
        while ($workDays->count() < 30) {
            if (!in_array($cursor->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $workDays->push($cursor->toDateString());
            }
            $cursor->subDay();
        }

        $attCount = 0;

        foreach ($workDays as $date) {
            foreach ($allStudents as $student) {
                // Skip if already exists for this student + date
                if (Attendance::where('student_id', $student->id)
                               ->where('date', $date)->exists()) {
                    continue;
                }

                // 78% Present | 14% Absent | 8% Late
                $rand   = rand(1, 100);
                $status = $rand <= 78 ? 'Present' : ($rand <= 92 ? 'Absent' : 'Late');

                Attendance::create([
                    'student_id' => $student->id,
                    'class_id'   => $student->class_id,
                    'date'       => $date,
                    'status'     => $status,
                    'marked_by'  => $adminId,
                ]);
                $attCount++;
            }
        }

        $this->command->info("  ✅ {$attCount} attendance records seeded");
        $this->command->info("     Students: {$allStudents->count()}");
        $this->command->info("     Days: 30 working days");
    }
}
