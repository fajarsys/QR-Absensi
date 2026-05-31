<?php

namespace App\Http\Controllers;

use App\Services\HistoryService;
use Illuminate\Support\Facades\Auth;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;

#[middleware(['web', 'auth'])]
class HistoryController extends Controller
{
    #[Get('/riwayat-kehadiran', name:'riwayat-kehadiran')]
    public function Index (HistoryService $service) {

        $riwayatAbsen = $service->getUserHistory(Auth::id());


        return view('user.history', compact('riwayatAbsen'));
    }
}
