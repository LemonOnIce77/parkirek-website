<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;
use App\Models\ParkingRecord;
use App\Models\Pelanggaran;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraans';

    protected $fillable = [
        'user_id',
        'jenis_kendaraan',
        'plat_no',
        'stnk_number',
        'stnk_photo_path', // ✅ tambah untuk upload STNK
        'qr_token',
    ];

    /**
     * URL siap pakai untuk menampilkan foto STNK di blade.
     * contoh: <img src="{{ $kendaraan->stnk_photo_url }}">
     */
    public function getStnkPhotoUrlAttribute(): ?string
    {
        return $this->stnk_photo_path
            ? asset('storage/' . $this->stnk_photo_path)
            : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parkingRecords(): HasMany
    {
        return $this->hasMany(ParkingRecord::class, 'kendaraan_id');
    }

    public function pelanggarans(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'kendaraan_id');
    }
}
