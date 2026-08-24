<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'late_tolerance',

    ];

    // Relasi ke user yang menggunakan jadwal shift ini
    public function userSchedules()
    {
        return $this->hasMany(UserSchedule::class);
    }
}