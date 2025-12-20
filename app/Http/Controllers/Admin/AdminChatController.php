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
    public function fetchUsers()
    {
        // دریافت لیست ادمین‌ها به جز خود کاربر فعلی (یا همه، بسته به سلیقه)
        $admins = \App\Models\Admin::select('id', 'name', 'is_online', 'profile_photo_path')
            ->where('id', '!=', Auth::guard('admin')->id()) // اگر نمی‌خواهید خودتان را در لیست ببینید
            ->get();
        
        // وضعیت خود کاربر جاری
        $me = Auth::guard('admin')->user();

        return response()->json([
            'admins' => $admins,
            'my_status' => $me->is_online
        ]);
    }

    public function toggleStatus(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $admin->is_online = !$admin->is_online;
        $admin->save();

        return response()->json(['status' => $admin->is_online]);
    }
}