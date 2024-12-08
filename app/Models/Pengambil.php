<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengambil extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'peminjaman_id',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(peminjaman::class, 'peminjaman_id', 'id');
    }

    public function barangKeluar(): HasMany
    {
        return $this->hasMany(BarangKeluar::class);
    }
}
