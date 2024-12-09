<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipeLokasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
    ];

    public function barangKeluar(): HasMany
    {
        return $this->hasMany(BarangKeluar::class);
    }

    public function lokasi(): HasMany
    {
        return $this->hasMany(Lokasi::class);
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(PeminjamanBarang::class);
    }
}
