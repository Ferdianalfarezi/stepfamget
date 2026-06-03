<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenerimaanBarang extends Model
{
    protected $table = 'penerimaan_barang';

    protected $fillable = [
        'supplier_id',
        'barang',
        'harga',
        'qty',
        'total',
        'pic',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'qty'   => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}