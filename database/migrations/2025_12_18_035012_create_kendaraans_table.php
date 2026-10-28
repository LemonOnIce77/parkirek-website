<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('jenis_kendaraan', ['motor', 'mobil']);
            $table->string('plat_no', 20)->unique();
            $table->string('stnk_number', 50);

            // opsional: foto STNK
            $table->string('stnk_photo_path')->nullable();

            // dipakai scan QR
            $table->uuid('qr_token')->unique();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
