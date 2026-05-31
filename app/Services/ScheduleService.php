<?php

namespace App\Services;

use App\Models\User;
use App\Models\Schedule;
use App\Models\UserSchedule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserScheduleImport;


class ScheduleService
{
    /**
     * Create a new class instance.
     */
    public function getUserSchedules(int $userId)
    {
        return UserSchedule::with('schedule')
            ->where('user_id', $userId)
            ->get();
    }

    public function userAllSchedule() {
        return [
            'users' => User::all(),
            'schedules' => Schedule::all(),
            'userSchedules' => UserSchedule::with(['user', 'schedule'])->get()
        ];
    }

    public function storeManual(array $data)
    {
        return UserSchedule::create($data);
    }

    public function getEditData($id): array
    {
        return [
            'userSchedule' => UserSchedule::findOrFail($id),
            'schedules' => Schedule::all()
        ];
    }

    public function updateSchedule($id, array $data)
    {
        $userSchedule = UserSchedule::findOrFail($id);
        return $userSchedule->update($data);
    }

    public function destroySchedule($id)
    {
        $userSchedule = UserSchedule::findOrFail($id);
        return $userSchedule->delete();
    }

    public function destroyMonth(array $data)
    {
        return UserSchedule::whereMonth('schedule_date', $data['month'])
            ->whereYear('schedule_date', $data['year'])
            ->delete();
    }

    public function importExcel($file)
    {
        Excel::import(new UserScheduleImport, $file);
    }

   
}
