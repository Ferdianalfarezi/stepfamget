<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GanttActivity extends Model
{
    protected $fillable = [
        'activity',
        'detail',
        'is_highlight',
        'plan_start_month',
        'plan_start_week',
        'plan_end_month',
        'plan_end_week',
        'actual_start_month',
        'actual_start_week',
        'actual_end_month',
        'actual_end_week',
        'urutan',
    ];

    protected $casts = [
        'plan_start_month'   => 'integer',
        'plan_start_week'    => 'integer',
        'plan_end_month'     => 'integer',
        'plan_end_week'      => 'integer',
        'actual_start_month' => 'integer',
        'actual_start_week'  => 'integer',
        'actual_end_month'   => 'integer',
        'actual_end_week'    => 'integer',
        'urutan'             => 'integer',
        'is_highlight'       => 'boolean',
    ];
    
    public static $months = [
        1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr',
        5=>'Mei', 6=>'Jun', 7=>'Jul', 8=>'Ags',
        9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des',
    ];

    // Convert bulan+minggu ke index global (1–48) untuk perbandingan
    public static function toGlobal(int $month, int $week): int
    {
        return ($month - 1) * 4 + $week;
    }

    // Cek apakah week global tertentu masuk dalam range plan
    public function inPlanRange(int $month, int $week): bool
    {
        $g     = self::toGlobal($month, $week);
        $start = self::toGlobal($this->plan_start_month, $this->plan_start_week);
        $end   = self::toGlobal($this->plan_end_month,   $this->plan_end_week);
        return $g >= $start && $g <= $end;
    }

    public function inActualRange(int $month, int $week): bool
    {
        if (!$this->actual_start_month || !$this->actual_end_month) return false;
        $g     = self::toGlobal($month, $week);
        $start = self::toGlobal($this->actual_start_month, $this->actual_start_week);
        $end   = self::toGlobal($this->actual_end_month,   $this->actual_end_week);
        return $g >= $start && $g <= $end;
    }
}