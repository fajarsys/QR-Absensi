<?php

namespace App\Http\Controllers;

use App\Services\AdminDashboardService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    protected $adminDashboardService;

    public function __construct(AdminDashboardService $adminDashboardService)
    {
        $this->adminDashboardService = $adminDashboardService;
    }

    public function index()
    {
        // Pendelegasian logika ke Service Pattern agar Controller tetap tipis (Thin Controller)
        $recentActivities = $this->adminDashboardService->getRecentActivities();

        return view('admin.dashboard', compact('recentActivities'));
    }
}
