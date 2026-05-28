<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Subject;
use App\Models\Notice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding sample data...');

        // ── Classes ───────────────────────────────────────────────────────
        $classesData = [
            ['name' => 'Play Group',  'section' => 'A'],
            ['name' => 'Nursery',     'section' => 'A'],
            ['name' => 'Prep',        'section' => 'A'],
            ['name' => 'Class 1',     'section' => 'A'],
            ['name' => 'Class 1',     'section' => 'B'],
            ['name' => 'Class 2',     'section' => 'A'],
            ['name' => 'Class 3',     'section' => 'A'],
            ['name' => 'Class 4',     'section' => 'A'],
            ['name' => 'Class 5',     'section' => 'A'],
            ['name' => 'Class 6',     'section' => 'A'],
            ['name' => 'Class 7',     'section' => 'A'],
            ['name' => 'Class 8',     'section' => 'A'],
        ];

        $classes = [];
        foreach ($classesData as $c) {
            $classes[] = ClassRoom::firstOrCreate(
                ['name' => $c['name'], 'section' => $c['section']],
                ['status' => true]
            );
        }
        $this->command->info('  ✅ ' . count($classes) . ' classes created');

        // ── Subjects ──────────────────────────────────────────────────────
        $subjectNames = ['English', 'Urdu', 'Mathematics', 'Science', 'Social Studies', 'Islamiyat', 'Computer'];
        foreach ($classes as $class) {
            foreach ($subjectNames as $i => $name) {
                Subject::firstOrCreate(
                    ['class_id' => $class->id, 'name' => $name],
                    ['code' => strtoupper(substr($name, 0, 3)) . $class->id, 'status' => true]
                );
            }
        }
        $this->command->info('  ✅ Subjects created');

        // ── Teachers ─────────────────────────────────────────────────────
        $teachersData = [
            ['name' => 'Imran Ahmed',    'email' => 'imran@educore.test',   'qual' => 'M.Ed',  'spec' => 'Mathematics'],
            ['name' => 'Sana Malik',     'email' => 'sana@educore.test',    'qual' => 'B.Ed',  'spec' => 'English'],
            ['name' => 'Tariq Hussain',  'email' => 'tariq@educore.test',   'qual' => 'M.Sc',  'spec' => 'Science'],
            ['name' => 'Fatima Zahra',   'email' => 'fatima@educore.test',  'qual' => 'B.A',   'spec' => 'Urdu'],
            ['name' => 'Usman Khan',     'email' => 'usman@educore.test',   'qual' => 'M.Phil','spec' => 'Social Studies'],
            ['name' => 'Ayesha Siddiqi', 'email' => 'ayesha@educore.test',  'qual' => 'B.Ed',  'spec' => 'Islamiyat'],
        ];

        foreach ($teachersData as $t) {
            if (!User::where('email', $t['email'])->exists()) {
                $user = User::create([
                    'name'     => $t['name'],
                    'email'    => $t['email'],
                    'password' => Hash::make('teacher123'),
                    'role'     => 'teacher',
                    'status'   => 'active',
                ]);
                Teacher::create([
                    'user_id'               => $user->id,
                    'qualification'         => $t['qual'],
                    'subject_specialization'=> $t['spec'],
                    'joining_date'          => now()->subMonths(rand(6, 36))->toDateString(),
                    'status'                => true,
                ]);
            }
        }
        $this->command->info('  ✅ ' . count($teachersData) . ' teachers created');

        // ── Assign Teachers to Subjects (by specialization) ───────────────
        $specToSubject = [
            'Mathematics'   => 'Mathematics',
            'English'       => 'English',
            'Science'       => 'Science',
            'Urdu'          => 'Urdu',
            'Social Studies'=> 'Social Studies',
            'Islamiyat'     => 'Islamiyat',
        ];

        foreach ($teachersData as $t) {
            $user    = User::where('email', $t['email'])->first();
            $teacher = $user?->teacher;
            if (!$teacher) continue;

            $subjectName = $specToSubject[$t['spec']] ?? null;
            if (!$subjectName) continue;

            // Assign this teacher to ALL subjects matching their specialization
            Subject::where('name', $subjectName)->update(['teacher_id' => $teacher->id]);
        }
        $this->command->info('  ✅ Teachers assigned to subjects by specialization');

        // ── Students ─────────────────────────────────────────────────────
        $maleNames = [
            'Muhammad Ali',    'Ahmed Hassan',    'Usman Tariq',     'Bilal Khan',
            'Hamza Raza',      'Zain Ul Abideen', 'Fahad Siddiqui',  'Omar Farooq',
            'Saad Mehmood',    'Anas Qureshi',    'Ibrahim Malik',   'Yousuf Iqbal',
            'Talha Bajwa',     'Haroon Rasheed',  'Fawad Chaudhry',  'Kamran Akhtar',
            'Danish Saleem',   'Waqas Javed',     'Nouman Riaz',     'Shahzaib Ahmed',
        ];

        $femaleNames = [
            'Fatima Malik',    'Ayesha Raza',     'Zainab Hussain',  'Maryam Khan',
            'Hira Baig',       'Sana Tariq',      'Nimra Siddiqui',  'Noor Ul Ain',
            'Sara Qureshi',    'Amna Javed',      'Rabia Mehmood',   'Mahnoor Iqbal',
            'Iqra Shahid',     'Sumayya Farooq',  'Laiba Farhan',    'Tooba Hassan',
            'Alina Rehman',    'Bisma Ashraf',    'Sidra Arif',      'Wardah Cheema',
        ];

        $guardians = [
            'Mr. Ali Hassan',     'Mr. Tariq Mehmood',  'Mr. Raza Ahmed',
            'Mr. Khan Iqbal',     'Mr. Malik Siddiqui', 'Mr. Farooq Javed',
            'Mr. Qureshi Aftab',  'Mr. Bajwa Saleem',   'Mr. Riaz Akbar',
            'Mr. Shahzad Anwar',
        ];

        $studentCount = 0;
        $rollCounter  = 1001;

        foreach ($classes as $class) {
            // 6 students per class (3 male, 3 female)
            for ($i = 0; $i < 6; $i++) {
                $isFemale  = ($i >= 3);
                $namePool  = $isFemale ? $femaleNames : $maleNames;
                $name      = $namePool[($class->id * 6 + $i) % count($namePool)];
                $email     = strtolower(str_replace(' ', '.', $name)) . $rollCounter . '@student.test';
                $gender    = $isFemale ? 'Female' : 'Male';

                if (!User::where('email', $email)->exists()) {
                    $user = User::create([
                        'name'     => $name,
                        'email'    => $email,
                        'password' => Hash::make('student123'),
                        'role'     => 'student',
                        'status'   => (rand(0, 9) > 1) ? 'active' : 'inactive', // 80% active
                    ]);

                    Student::create([
                        'user_id'        => $user->id,
                        'class_id'       => $class->id,
                        'roll_no'        => 'STU-' . $rollCounter,
                        'guardian_name'  => $guardians[array_rand($guardians)],
                        'guardian_phone' => '0300-' . rand(1000000, 9999999),
                        'address'        => rand(1, 99) . ' Block ' . chr(rand(65, 72)) . ', Lahore',
                        'gender'         => $gender,
                        'date_of_birth'  => now()->subYears(rand(5, 16))->subDays(rand(0, 365))->toDateString(),
                        'admission_date' => now()->subMonths(rand(1, 24))->toDateString(),
                        'status'         => true,
                    ]);

                    $studentCount++;
                    $rollCounter++;
                }
            }
        }
        $this->command->info("  ✅ {$studentCount} students created");

        // ── Notices ───────────────────────────────────────────────────────
        $noticesData = [
            ['title' => 'Annual Day Celebration', 'content' => 'Annual Day will be held on 30th May 2026. All students must report by 8:00 AM in uniform. Parents are cordially invited.', 'target' => 'all'],
            ['title' => 'Fee Submission Deadline', 'content' => 'Last date for fee submission is 5th June 2026. Late fee Rs. 200 will be charged after the due date.', 'target' => 'all'],
            ['title' => 'Exam Schedule Released', 'content' => 'Mid-term examination schedule has been released. Students can collect their admit cards from the admin office.', 'target' => 'student'],
            ['title' => 'Staff Meeting', 'content' => 'Mandatory staff meeting on Friday 29th May at 1:00 PM in the conference room. All teachers must attend.', 'target' => 'teacher'],
            ['title' => 'Holiday Notice', 'content' => 'School will remain closed on 1st June 2026 due to public holiday. Classes will resume on 2nd June 2026.', 'target' => 'all'],
        ];

        $adminUser = User::where('role', 'admin')->first();
        foreach ($noticesData as $n) {
            Notice::firstOrCreate(
                ['title' => $n['title']],
                [
                    'content'    => $n['content'],
                    'target_role'=> $n['target'],
                    'status'     => true,
                    'created_by' => $adminUser?->id ?? 1,
                ]
            );
        }
        $this->command->info('  ✅ ' . count($noticesData) . ' notices created');

        // ── Attendance ────────────────────────────────────────────────────────
        $this->command->info('  🌱 Seeding attendance (30 working days)...');
        $adminId     = $adminUser?->id ?? 1;
        $allStudents = Student::where('status', true)->get();
        $attCount    = 0;

        // Generate past 30 working days (Mon–Fri, skip weekends)
        $workDays = collect();
        $cursor   = Carbon::today()->subDays(1);
        while ($workDays->count() < 30) {
            if (!in_array($cursor->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $workDays->push($cursor->toDateString());
            }
            $cursor->subDay();
        }

        foreach ($workDays as $date) {
            foreach ($allStudents as $student) {
                // Skip if already seeded
                if (Attendance::where('student_id', $student->id)->where('date', $date)->exists()) {
                    continue;
                }
                // 78% Present, 14% Absent, 8% Late
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
        $this->command->info("  ✅ {$attCount} attendance records created");

        $this->command->info('');
        $this->command->info('✅ Sample data seeded successfully!');
        $this->command->info('   Classes: ' . count($classes));
        $this->command->info('   Teachers: ' . count($teachersData) . ' (password: teacher123)');
        $this->command->info('   Students: ' . $studentCount . ' (password: student123)');
        $this->command->info('   Notices: ' . count($noticesData));
        $this->command->info('   Attendance records: ' . $attCount);
    }
}
