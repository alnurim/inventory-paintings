<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeminjamanBarang extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'tipe_lokasi_id',
        'tanggal_peminjaman',
        'tanggal_pengembalian',
        'kuantitas',
        'nama',
        'status',
        'keterangan',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id');
    }

    public function tipeLokasi(): BelongsTo
    {
        return $this->belongsTo(TipeLokasi::class, 'tipe_lokasi_id', 'id');
    }

    public function pengambil(): HasMany
    {
        return $this->hasMany(Pengambil::class);
    }
}
