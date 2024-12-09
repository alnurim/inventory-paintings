<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lokasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tipe_lokasi_id',
    ];

    public function tipeLokasi(): BelongsTo
    {
        return $this->belongsTo(TipeLokasi::class, 'tipe_lokasi_id', 'id');
    }
}
