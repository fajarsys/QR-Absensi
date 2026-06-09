<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LeaveService;
use App\Http\Resources\LeaveResource;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\ApproveLeaveRequest;
use App\Models\Leave;

class LeaveController extends Controller
{
    public function createIzin(LeaveService $service)
    {
        $riwayat = $service->getLeavesByUser(Auth::id());
        return view('user.izin', [
            'kategori' => 'izin',
            'riwayat'  => $riwayat
        ]);
    }
    public function adminIndex(LeaveService $service, \App\Services\OvertimeService $overtimeService)
    {
        $leaves = $service->getAllLeavesForAdmin();
        $overtimes = $overtimeService->getAllOvertimesForAdmin();
        return view('admin.izin.index', compact('leaves', 'overtimes'));
    }


    public function store(StoreLeaveRequest $request, LeaveService $service)
    {
        $izinBaru = $service->createLeave(
            $request->validated(),
            Auth::id(),
            $request->file('attachment')
        );

        return redirect()->back()->with('success', 'Pengajuan berhasil dikirim. Menunggu persetujuan Admin.');


        // return response()->json([
        //     'message' => 'Pengajuan berhasil',
        //     'data' => new LeaveResource($izinBaru)
        // ], 201);
    }

    /**
     * Prints the user details.
     * @param int|string $id The ID of the user.
     */
    public function approve(ApproveLeaveRequest $request, $id, LeaveService $service)
    {

        $service->updateLeaveStatus($id, $request->status);

        return redirect()->back()->with('success', 'Status pengajuan izin berhasil diperbarui.');
    }

    /**
     * Prints the user details.
     * @param int|string $id The ID of the user.
     */
    public function destroy($id, LeaveService $service)
    {
        $service->deleteLeave($id);
        return redirect()->back()->with('success', 'Data izin beserta dokumennya berhasil dihapus.');
    }
}
