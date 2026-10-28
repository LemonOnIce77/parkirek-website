<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parking_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kendaraan_id')
                ->constrained('kendaraans')
                ->cascadeOnDelete();

            // admin/satpam yang scan
            $table->foreignId('scanned_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->dateTime('jam_masuk');
            $table->dateTime('jam_keluar')->nullable();

            // snapshot saat scan masuk
            $table->string('plat_snapshot', 20);
            $table->string('stnk_snapshot', 50);

            $table->enum('status', ['ACTIVE', 'DONE'])->default('ACTIVE');

            $table->timestamps();

            // sesuai kebutuhan kamu
            $table->index(['kendaraan_id', 'jam_masuk']);

            // membantu query scan keluar (optional tapi bagus)
            $table->index(['kendaraan_id', 'status', 'jam_keluar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_records');
    }
};
