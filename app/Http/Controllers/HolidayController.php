<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('holiday_date', 'desc')->get();
        return view('admin.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'holiday_date' => 'required|date|unique:holidays,holiday_date',
            'description' => 'nullable|string|max:255',
        ], [
            'holiday_date.unique' => 'Tanggal merah tersebut sudah terdaftar di sistem.',
        ]);

        Holiday::create($request->all());

        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil dihapus!');
    }
}
