<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(DashboardService $service)
    {
        ['todayAttendance' => $todayAttendance, 'lateMinutes' => $lateMinutes]= $service->getTodayDashboardData(Auth::id());
        
        return view('dashboard', compact('todayAttendance', 'lateMinutes'));
    }
}

