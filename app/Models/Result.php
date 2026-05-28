<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'exam_id',
        'obtained_marks',
        'total_marks',
        'percentage',
        'grade',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
