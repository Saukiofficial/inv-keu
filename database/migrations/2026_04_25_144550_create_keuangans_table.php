<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangans', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['Masuk', 'Keluar']);
            $table->string('kategori'); // gaji, operasional, pemasukan pelanggan, dll
            $table->decimal('jumlah', 15, 2);
            $table->string('keterangan')->nullable();
            $table->date('tanggal');
            $table->string('bukti')->nullable(); // upload foto/file bukti
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangans');
    }
};