<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOvertimeRequest;
use App\Services\OvertimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Overtime;

class OvertimeController extends Controller
{
    protected $overtimeService;

    // Suntikkan OvertimeService ke dalam Controller (Dependency Injection)
    public function __construct(OvertimeService $overtimeService)
    {
        $this->overtimeService = $overtimeService;
    }

    public function createLembur()
    {
        $riwayat = Overtime::where('user_id', Auth::id())->latest()->get();
        return view('user.izin', [
            'kategori' => 'lembur',
            'riwayat'  => $riwayat
        ]);
    }

    public function store(StoreOvertimeRequest $request)
    {
        try {
            // Data sudah tervalidasi otomatis oleh StoreOvertimeRequest
            $validatedData = $request->validated();

            // Lempar data ke Service untuk dikalkulasi dan disimpan
            $this->overtimeService->submitPengajuanLembur($validatedData);

            // Jika API (opsional):
            // return response()->json(['message' => 'Pengajuan lembur berhasil dikirim.'], 201);

            // Jika menggunakan tampilan web/Blade:
            return redirect()->back()->with('success', 'Pengajuan lembur berhasil dikirim dan menunggu persetujuan.');
        } catch (\Exception $e) {
            // Tangkap jika terjadi error sistem/database
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function approve(Request $request, $id)
    {
        $status = $request->input('status'); // 'approved' atau 'rejected'
        $this->overtimeService->updateOvertimeStatus($id, $status);

        return redirect()->back()->with('success', 'Status pengajuan lembur berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->overtimeService->deleteOvertime($id);

        return redirect()->back()->with('success', 'Data pengajuan lembur berhasil dihapus.');
    }
}
