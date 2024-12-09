<?php

namespace App\Observers;

use App\Models\PeminjamanBarang;

class PeminjamanBarangObserver
{
    /**
     * Handle the PeminjamanBarang "created" event.
     */
    public function created(PeminjamanBarang $peminjamanBarang): void
    {
        $barang = $peminjamanBarang->barang;
        if ($barang) {
            // Kurangi kuantitas barang sesuai dengan kuantitas peminjaman
            $barang->decrement('kuantitas', $peminjamanBarang->kuantitas);
        }
    }

    /**
     * Handle the PeminjamanBarang "updated" event.
     */
    public function updated(PeminjamanBarang $peminjamanBarang): void
    {
        $barang = $peminjamanBarang->barang;

        if ($barang) {
            // Periksa apakah status berubah
            if ($peminjamanBarang->isDirty('status')) {
                if ($peminjamanBarang->status) {
                    // Jika status berubah menjadi true (pengembalian dilakukan)
                    $barang->increment('kuantitas', $peminjamanBarang->kuantitas);
                } else {
                    // Jika status berubah menjadi false (barang dipinjam kembali)
                    $barang->decrement('kuantitas', $peminjamanBarang->kuantitas);
                }
            }

            // Jika kuantitas diperbarui tanpa mengubah status
            if ($peminjamanBarang->isDirty('kuantitas')) {
                $originalKuantitas = $peminjamanBarang->getOriginal('kuantitas');
                $difference = $peminjamanBarang->kuantitas - $originalKuantitas;

                if ($peminjamanBarang->status) {
                    // Pengembalian, sesuaikan kuantitas barang
                    $barang->increment('kuantitas', $difference);
                } else {
                    // Peminjaman, kurangi kuantitas barang
                    $barang->decrement('kuantitas', $difference);
                }
            }
        }
    }

    /**
     * Handle the PeminjamanBarang "deleted" event.
     */
    public function deleted(PeminjamanBarang $peminjamanBarang): void
    {
        $barang = $peminjamanBarang->barang;
        if ($barang) {
            if ($peminjamanBarang->status) {
                return;
            } else {
                $barang->decrement('kuantitas', $peminjamanBarang->kuantitas);
            }
        }
    }

    /**
     * Handle the PeminjamanBarang "restored" event.
     */
    public function restored(PeminjamanBarang $peminjamanBarang): void
    {
        //
    }

    /**
     * Handle the PeminjamanBarang "force deleted" event.
     */
    public function forceDeleted(PeminjamanBarang $peminjamanBarang): void
    {
        //
    }
}
