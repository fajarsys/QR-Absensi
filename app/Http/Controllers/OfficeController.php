<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOfficeRequest;
use App\Services\OfficeService;
use GuzzleHttp\Psr7\Request;

class OfficeController extends Controller
{
    public function index(OfficeService $service)
    {
        $office = $service->officeIndex();

        return view('admin.office.index', compact('office'));
    }

    public function update(UpdateOfficeRequest $request, OfficeService $service, $id)
    {
        $service->updateOffice($id, $request->validated());

        return redirect()->back()->with('success', 'Data lokasi kantor berhasil diperbarui!');
    }

    public function qrDisplay()
    {
        return view('admin.office.qr_display');
    }

    public function renderQrCode()
    {
        $secret = config('app.qr_secret');
        $window = floor(time() / 15);
        $qrString = hash('sha256', $secret . $window);

        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrString);
        
        return response($svg)->header('Content-Type', 'image/svg+xml');
    }
}
