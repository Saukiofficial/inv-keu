<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('nama_asset');
            $table->string('kategori'); // router, switch, kabel, ODP, dll
            $table->string('merk')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('lokasi')->nullable(); // lokasi pemasangan
            $table->enum('kondisi', ['Baik', 'Rusak', 'Dalam Perbaikan'])->default('Baik');
            $table->date('tanggal_beli')->nullable();
            $table->decimal('harga', 15, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};