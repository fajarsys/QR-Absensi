<?php

namespace App\Services;
use App\Models\Office;

class OfficeService
{
    /**
     * Create a new class instance.
     */
    public function officeIndex() {
        $office = Office::first();

        if (!$office) {
            $office = Office::create([
                'name'      => 'Stmik Mercusuar',
                'latitude'  => -6.2797231171037255,
                'longitude' => 106.91323756907234,
                'radius'    => 30
            ]);
        }

        return $office;
    }

    public function updateOffice (int $id, array $data) {

        $office = Office::findOrfail($id);

        $office->update($data);

    }
}
