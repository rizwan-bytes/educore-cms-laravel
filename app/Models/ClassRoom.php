<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'section',
        'status',
        'attendance_mode',
        'incharge_teacher_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // ── Relations ────────────────────────────────────────────────────────────
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function inchargeTeacher()
    {
        return $this->belongsTo(Teacher::class, 'incharge_teacher_id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────
    public function isClassIncharge(): bool
    {
        return $this->attendance_mode === 'class_incharge';
    }

    public function isSubjectWise(): bool
    {
        return $this->attendance_mode === 'subject_wise';
    }

    public function getModeLabelAttribute(): string
    {
        return $this->attendance_mode === 'class_incharge'
            ? __('classes.mode_class_incharge')
            : __('classes.mode_subject_wise');
    }
}
