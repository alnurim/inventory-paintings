<?php

namespace App\Observers;

use App\Models\BarangKeluar;

class BarangKeluarObserver
{
    /**
     * Handle the BarangKeluar "created" event.
     */
    public function created(BarangKeluar $barangKeluar): void
    {
        $barang = $barangKeluar->barang;
        if ($barang) {
            $barang->decrement('kuantitas', $barangKeluar->kuantitas);
        }
    }

    /**
     * Handle the BarangKeluar "updated" event.
     */
    public function updated(BarangKeluar $barangKeluar): void
    {
        $original = $barangKeluar->getOriginal('kuantitas');
        $difference = $barangKeluar->kuantitas - $original;

        $barang = $barangKeluar->barang;
        if ($barang && $difference !== 0) {
            return;
        }
    }

    /**
     * Handle the BarangKeluar "deleted" event.
     */
    public function deleted(BarangKeluar $barangKeluar): void
    {
        $barang = $barangKeluar->barang;
        if ($barang) {
            $barang->increment('kuantitas', $barangKeluar->kuantitas);
        }
    }

    /**
     * Handle the BarangKeluar "restored" event.
     */
    public function restored(BarangKeluar $barangKeluar): void
    {
        //
    }

    /**
     * Handle the BarangKeluar "force deleted" event.
     */
    public function forceDeleted(BarangKeluar $barangKeluar): void
    {
        //
    }
}
