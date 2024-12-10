<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'karyawan_id',
        'tipe_lokasi_id',
        'tanggal',
        'kuantitas',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }

    public function tipeLokasi(): BelongsTo
    {
        return $this->belongsTo(TipeLokasi::class, 'tipe_lokasi_id', 'id');
    }

    public function pemakaianLapangan(): HasMany
    {
        return $this->hasMany(PemakaianLapangan::class);
    }
}
