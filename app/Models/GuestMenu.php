<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;  // ← ini yang bener

class GuestMenu extends Model
{
    protected $fillable = [
        'key', 'label', 'icon', 'bg_color', 'color',
        'urutan', 'is_active', 'berlaku_hingga',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'berlaku_hingga' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->berlaku_hingga !== null
            && $this->berlaku_hingga->isPast();
    }

    public static function getActive(): Collection
    {
        return static::where('is_active', true)
            ->orderBy('urutan')
            ->get();
    }
}