<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClassRoom;
use App\Models\Subject;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Subjects assigned to this teacher
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    // Unique classes where this teacher has subjects
    public function assignedClasses()
    {
        return ClassRoom::whereIn('id',
            $this->subjects()->where('status', true)->pluck('class_id')->unique()
        )->where('status', true)->orderBy('name')->get();
    }
}
