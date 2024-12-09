<?php

namespace App\Observers;

use App\Models\BarangMasuk;

class BarangMasukObserver
{
    /**
     * Handle the BarangMasuk "created" event.
     */
    public function created(BarangMasuk $barangMasuk): void
    {
        $barang = $barangMasuk->barang;
        if ($barang) {
            $barang->increment('kuantitas', $barangMasuk->kuantitas);
        }
    }

    /**
     * Handle the BarangMasuk "updated" event.
     */
    public function updated(BarangMasuk $barangMasuk): void
    {
        $barang = $barangMasuk->barang;
        if ($barang) {
            // Hitung perubahan kuantitas
            $difference = $barangMasuk->kuantitas - $barangMasuk->getOriginal('kuantitas');
            $barang->increment('kuantitas', $difference);
        }
    }

    /**
     * Handle the BarangMasuk "deleted" event.
     */
    public function deleted(BarangMasuk $barangMasuk): void
    {
        $barang = $barangMasuk->barang;
        if ($barang) {
            $barang->decrement('kuantitas', $barangMasuk->kuantitas);
        }
    }

    /**
     * Handle the BarangMasuk "restored" event.
     */
    public function restored(BarangMasuk $barangMasuk): void
    {
        //
    }

    /**
     * Handle the BarangMasuk "force deleted" event.
     */
    public function forceDeleted(BarangMasuk $barangMasuk): void
    {
        //
    }
}
