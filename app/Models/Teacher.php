<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'qualification',
        'subject_specialization',
        'joining_date',
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
    ];

    // ── Relations ─────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    // Classes where this teacher is set as class incharge
    public function inchargeClasses()
    {
        return $this->hasMany(ClassRoom::class, 'incharge_teacher_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────
    /**
     * All classes this teacher can mark attendance for:
     *   ① Classes where mode=class_incharge AND incharge_teacher_id = this teacher
     *   ② Classes where mode=subject_wise   AND teacher has ≥1 subject in that class
     */
    public function assignedClasses()
    {
        // ① Incharge classes (class_incharge mode)
        $inchargeIds = ClassRoom::where('attendance_mode', 'class_incharge')
            ->where('incharge_teacher_id', $this->id)
            ->where('status', true)
            ->pluck('id');

        // ② Subject-wise classes (teacher has a subject here)
        $subjectClassIds = $this->subjects()
            ->where('status', true)
            ->pluck('class_id')
            ->unique();

        // Subject-wise classes where the class itself is in subject_wise mode
        $subjectWiseIds = ClassRoom::where('attendance_mode', 'subject_wise')
            ->where('status', true)
            ->whereIn('id', $subjectClassIds)
            ->pluck('id');

        // Merge both, unique, fetch
        $allIds = $inchargeIds->merge($subjectWiseIds)->unique();

        return ClassRoom::whereIn('id', $allIds)
            ->orderBy('name')
            ->get();
    }

    /**
     * Check if teacher can mark attendance for a given class
     */
    public function canMarkAttendance(ClassRoom $class): bool
    {
        if ($class->attendance_mode === 'class_incharge') {
            return $class->incharge_teacher_id === $this->id;
        }

        // subject_wise: teacher must have at least 1 subject in this class
        return $this->subjects()
            ->where('class_id', $class->id)
            ->where('status', true)
            ->exists();
    }

    /**
     * Get subjects for a class (used in subject-wise mode)
     */
    public function subjectsForClass(int $classId)
    {
        return $this->subjects()
            ->where('class_id', $classId)
            ->where('status', true)
            ->get();
    }
}
