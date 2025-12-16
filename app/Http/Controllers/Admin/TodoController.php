<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function index()
    {
        // دریافت کارهای مربوط به ادمین لاگین شده
        // کارهای ناتمام بالاتر و کارهایی که زمانشان نزدیکتر است اولویت دارند
        $todos = Todo::where('admin_id', Auth::guard('admin')->id())
            ->orderBy('is_completed', 'asc')
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json($todos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000', // اعتبارسنجی توضیحات
            'due_date' => 'required|date',
        ]);

        $todo = Todo::create([
            'admin_id' => Auth::guard('admin')->id(),
            'title' => $request->title,
            'description' => $request->description, // ذخیره توضیحات
            'due_date' => $request->due_date,
        ]);

        return response()->json($todo);
    }

    public function toggle(Todo $todo)
    {
        // امنیت: فقط صاحب تسک بتواند آن را تغییر دهد
        if ($todo->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        $todo->update(['is_completed' => !$todo->is_completed]);
        return response()->json(['status' => 'success']);
    }

    public function destroy(Todo $todo)
    {
        if ($todo->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        $todo->delete();
        return response()->json(['status' => 'success']);
    }

    public function checkUrgent()
    {
        // تسک‌هایی که:
        // ۱. مال خود ادمین است
        // ۲. تمام نشده‌اند
        // ۳. زمانشان نگذشته است (اختیاری) ولی نزدیک است (مثلاً زیر ۲ ساعت)
        // ۴. یا زمانشان گذشته ولی هنوز انجام نشده‌اند (Alert فوری)
        
        $urgentTodos = Todo::where('admin_id', Auth::guard('admin')->id())
            ->where('is_completed', false)
            ->where('due_date', '<', now()->addHours(2)) // کمتر از ۲ ساعت مانده یا گذشته
            ->get();

        return response()->json($urgentTodos);
    }
}