<?php

namespace App\Observers;

use App\Models\PemakaianLapangan;

class PemakaianLapanganObserver
{
    /**
     * Handle the PemakaianLapangan "created" event.
     */
    public function created(PemakaianLapangan $pemakaianLapangan): void
    {
        $barangKeluar = $pemakaianLapangan->barangKeluar;
        if ($barangKeluar) {
            // Kurangi kuantitas barang keluar
            $barangKeluar->decrement('kuantitas', $pemakaianLapangan->kuantitas);
        }
    }

    /**
     * Handle the PemakaianLapangan "updated" event.
     */
    public function updated(PemakaianLapangan $pemakaianLapangan): void
    {
        $original = $pemakaianLapangan->getOriginal('kuantitas');
        $difference = $pemakaianLapangan->kuantitas - $original;

        $barangKeluar = $pemakaianLapangan->barangKeluar;
        if ($barangKeluar) {
            // Sesuaikan hanya kuantitas barang keluar
            $barangKeluar->decrement('kuantitas', $difference);
        }
    }

    /**
     * Handle the PemakaianLapangan "deleted" event.
     */
    public function deleted(PemakaianLapangan $pemakaianLapangan): void
    {
        $barangKeluar = $pemakaianLapangan->barangKeluar;
        if ($barangKeluar) {
            // Tambahkan kembali kuantitas ke barang keluar
            $barangKeluar->increment('kuantitas', $pemakaianLapangan->kuantitas);
        }
    }

    /**
     * Handle the PemakaianLapangan "restored" event.
     */
    public function restored(PemakaianLapangan $pemakaianLapangan): void
    {
        //
    }

    /**
     * Handle the PemakaianLapangan "force deleted" event.
     */
    public function forceDeleted(PemakaianLapangan $pemakaianLapangan): void
    {
        //
    }
}
