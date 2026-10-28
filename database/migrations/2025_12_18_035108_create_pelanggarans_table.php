<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggarans', function (Blueprint $table) {
            $table->id();

            // optional: kalau plat_no ketemu di kendaraans
            $table->foreignId('kendaraan_id')
                ->nullable()
                ->constrained('kendaraans')
                ->nullOnDelete();

            // admin pembuat pelanggaran
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            // tetap simpan plat meski kendaraan tidak terdaftar
            $table->string('plat_no', 20);

            $table->string('jenis_pelanggaran', 100);
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('denda')->nullable();

            // bukti foto (public storage)
            $table->string('photo_path')->nullable();

            // konsisten dengan modul dashboard & filter API
            $table->enum('status', ['OPEN', 'CLOSED'])->default('OPEN');

            $table->timestamps();

            $table->index(['plat_no', 'status']);
            $table->index(['kendaraan_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggarans');
    }
};
