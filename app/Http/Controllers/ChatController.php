<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;


class ChatController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('chat.index');
    }

    public function send(Request $request)
    {
        WebSocketsRouter::broadcastToChannel('chat', [
            'user' => auth()->user()->name,
            'message' => $request->input('message'),
        ]);
    }

}
