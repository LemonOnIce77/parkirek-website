<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;
use App\Models\Kendaraan;

class ParkingRecord extends Model
{
    use HasFactory;

    protected $table = 'parking_records';

    protected $fillable = [
        'kendaraan_id',
        'scanned_by',
        'jam_masuk',
        'jam_keluar',
        'plat_snapshot',
        'stnk_snapshot',
        'status',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime',
        'jam_keluar' => 'datetime',
    ];

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
