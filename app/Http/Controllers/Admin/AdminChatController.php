<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminChat;
use App\Models\User; // یا مدل Admin شما
use Illuminate\Support\Facades\Auth;

class AdminChatController extends Controller
{
    public function fetchMessages()
    {
        // دریافت ۵۰ پیام آخر با اطلاعات فرستنده
        $messages = AdminChat::with('admin:id,name')->latest()->take(50)->get()->reverse()->values();
        return response()->json([
            'messages' => $messages,
            'current_user_id' => Auth::id()
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $chat = new AdminChat();
        $chat->admin_id = Auth::id();
        $chat->message = $request->message;
        $chat->save();

        return response()->json(['status' => 'success']);
    }
}