<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Kendaraan;
use App\Models\ParkingRecord;
use App\Models\Pelanggaran;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function kendaraans(): HasMany
    {
        return $this->hasMany(Kendaraan::class, 'user_id');
    }

    // record yang discan oleh admin ini (scanned_by)
    public function scannedParkingRecords(): HasMany
    {
        return $this->hasMany(ParkingRecord::class, 'scanned_by');
    }

    // pelanggaran yang dibuat admin ini (created_by)
    public function createdPelanggarans(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'created_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
}
