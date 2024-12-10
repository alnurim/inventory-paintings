<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->foreignId('tipe_lokasi_id')->constrained('tipe_lokasis')->cascadeOnDelete();
            $table->date('tanggal');
            $table->unsignedMediumInteger('kuantitas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluars');
    }
};
