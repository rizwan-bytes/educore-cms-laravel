<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'name',
        'phone',
        'cnic',
        'department',
        'designation',
        'joining_date',
        'salary',
        'photo',
        'status',
        'user_id',
        'is_seeded',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'salary'       => 'decimal:2',
        'status'       => 'boolean',
        'is_seeded'    => 'boolean',
    ];

    // ── Departments ───────────────────────────────────────────────────────
    public const DEPARTMENTS = [
        'administrative'   => 'Administrative',
        'finance'          => 'Finance',
        'academic_support' => 'Academic Support',
        'support'          => 'Support',
    ];

    // ── Attendance Statuses ───────────────────────────────────────────────
    public const ATTENDANCE_STATUSES = [
        'Present', 'Absent', 'Late', 'Half_Day', 'Leave',
    ];

    // ── Relations ─────────────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────
    public function getDepartmentLabelAttribute(): string
    {
        return self::DEPARTMENTS[$this->department] ?? ucfirst($this->department);
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo && file_exists(storage_path('app/public/' . $this->photo))) {
            return asset('storage/' . $this->photo);
        }
        // Initials placeholder — uses same palette as app
        $initial = strtoupper(substr($this->name, 0, 1));
        $colors  = ['6366f1', '10b981', 'f59e0b', '06b6d4', 'f97316', 'ec4899'];
        $color   = $colors[$this->id % count($colors)];
        return "https://ui-avatars.com/api/?name={$initial}&background={$color}&color=fff&size=80&bold=true";
    }
}
