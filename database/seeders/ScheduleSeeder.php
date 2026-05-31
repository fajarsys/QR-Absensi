<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\UserSchedule;
use App\Models\User;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run()
    {
        $shiftPagi = Schedule::create([
            'name'           => 'Shift Pagi',
            'start_time'     => '08:00:00',
            'end_time'       => '16:00:00',
            'late_tolerance' => 15
        ]);

        $shiftSiang = Schedule::create([
            'name'           => 'Shift Siang',
            'start_time'     => '14:00:00',
            'end_time'       => '21:00:00',
            'late_tolerance' => 15
        ]);

       
        $users = User::all();

        $hariIni = Carbon::now();
        
        foreach ($users as $user) {
            UserSchedule::create([
                'user_id'     => $user->id,
                'schedule_id' => $shiftPagi->id,
                'schedule_date' => $hariIni->toDateString()
            ]);

            UserSchedule::create([
                'user_id'     => $user->id,
                'schedule_id' => $shiftPagi->id,
                'schedule_date' => $hariIni->copy()->addDay()->toDateString()
            ]);

        }
        
    }
}