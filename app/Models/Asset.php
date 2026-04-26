<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'nama_asset',
        'kategori',
        'merk',
        'serial_number',
        'lokasi',
        'kondisi',
        'tanggal_beli',
        'harga',
        'catatan',
    ];

    protected $casts = [
        'tanggal_beli' => 'date',
        'harga' => 'decimal:2',
    ];
}