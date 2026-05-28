<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAttendance extends Model
{
    protected $table = 'staff_attendance';

    protected $fillable = [
        'staff_id',
        'date',
        'status',
        'remarks',
        'marked_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ── Relations ─────────────────────────────────────────────────────────
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function marker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    // ── Status Badge Helper ───────────────────────────────────────────────
    public static function statusColor(string $status): string
    {
        return match($status) {
            'Present'  => 'var(--green)',
            'Late'     => 'var(--yellow)',
            'Half_Day' => 'var(--orange)',
            'Leave'    => 'var(--cyan)',
            'Absent'   => 'var(--red)',
            default    => 'var(--muted)',
        };
    }
}
