<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemakaianLapangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_keluar_id',
        'karyawan_id',
        'kuantitas',
    ];

    public function barangKeluar(): BelongsTo
    {
        return $this->belongsTo(BarangKeluar::class, 'barang_keluar_id', 'id');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }
}
