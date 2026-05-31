<?php

namespace App\Services;

use App\Models\Attendance;

class HistoryService
{
    /**
     * Create a new class instance.
     */
    public function getUserHistory(int $userId, int $perPage = 10)
    {
        return Attendance::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
