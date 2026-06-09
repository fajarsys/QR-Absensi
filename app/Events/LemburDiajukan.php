<?php

namespace App\Events;

use App\Models\Overtime;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LemburDiajukan implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lembur;

    public function __construct(Overtime $lembur)
    {
        $this->lembur = $lembur->load('user');
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notification'),
        ];
    }

    public function broadcastAs()
    {
        return 'lembur.baru';
    }
}
