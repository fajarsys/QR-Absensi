<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;




class UserSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'schedule_date',
    ];

    protected function casts(): array
    {
        return [
            
            'schedule_date' =>'date',
        ];
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function schedule()
    {
        
        return $this->belongsTo(Schedule::class); 
    }
}