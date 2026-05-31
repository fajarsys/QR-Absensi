<?php

namespace App\Http\Controllers;

use App\Ai\Agents\Evaluasi;
use App\Http\Requests\SendChatRequest;
use App\Services\ChatService;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Middleware;

#[Prefix('admin')]
#[Middleware(['web', 'admin'])]
class AiController extends Controller
{
    #[Post('/send', name: 'admin.chat.send')]
    public function sendChat(SendChatRequest $request, ChatService $service)
    {
        $hasil=$service->processMessage($request->validated('message'));

        if (!$hasil['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $hasil['message']
            ], $hasil['code']);
        }

        // 3. Kembalikan balasan sukses (baik dari deteksi laporan maupun jawaban AI)
        return response()->json([
            'status' => 'success',
            'data' => $hasil['data']
        ], $hasil['code']);
    }

    
}