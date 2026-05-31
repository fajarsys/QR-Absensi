<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_izin' => $this->id,
            'karyawan' => $this->user->name,
            'jenis_izin' => strtoupper($this->type),
            'durasi' => $this->start_date . ' s/d ' . $this->end_date,
            'alasan' => $this->reason,
            'lampiran_url' => $this->attachment ? asset('storage/' . $this->attachment) : null,
            'status' => $this->status,
            'diajukan_pada' => $this->created_at->diffForHumans() // Output: "2 jam yang lalu"
        ];
    }
}
