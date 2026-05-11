<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestMenu extends Model
{
    protected $fillable = [
        'key', 'label', 'icon', 'color', 'bg_color', 'is_active', 'urutan'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getActive()
    {
        return static::where('is_active', true)->orderBy('urutan')->get();
    }
}