<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'npk',
        'jabatan',
    ];

    public function pemakaianLapangan(): HasMany
    {
        return $this->hasMany(PemakaianLapangan::class);
    }

    public function pengambil(): HasMany
    {
        return $this->hasMany(Pengambil::class);
    }
}
