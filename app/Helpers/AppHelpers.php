<?php

namespace App\Helpers;

class AppHelpers
{
    public static function calcGrade(float $obtained, float $total): string
    {
        $pct = ($total > 0) ? ($obtained / $total) * 100 : 0;

        return match (true) {
            $pct >= 90 => 'A+',
            $pct >= 80 => 'A',
            $pct >= 70 => 'B',
            $pct >= 60 => 'C',
            $pct >= 50 => 'D',
            default    => 'F',
        };
    }

    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '92' . substr($phone, 1);
        }
        return $phone;
    }

    public static function rollNo(int $id, string $prefix = null): string
    {
        $prefix = $prefix ?? config('app.roll_no_prefix', 'STU');
        return $prefix . str_pad($id, 4, '0', STR_PAD_LEFT);
    }

    public static function attendancePercent(int $present, int $total): float
    {
        return $total > 0 ? round(($present / $total) * 100, 1) : 0.0;
    }
}
