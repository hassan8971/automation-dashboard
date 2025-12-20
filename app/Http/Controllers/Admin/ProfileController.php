<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
// مدل ادمین را یوز کنید (اگر نیاز شد، ولی auth()->guard('admin')->user() خودش مدل درست را می‌دهد)
use App\Models\Admin; 

class ProfileController extends Controller
{
    public function edit()
    {
        // دریافت ادمین فعلی
        $user = Auth::guard('admin')->user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\Admin $user */
        $user = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB Max
            'dashboard_banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096', // 4MB Max
        ]);

        // آپدیت اطلاعات متنی
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->nickname = $request->nickname;
        // چک‌باکس در فرم اگر تیک نخورد در ریکوئست نیست، پس اینطوری هندل می‌کنیم:
        $user->use_nickname = $request->has('use_nickname');

        // آپلود عکس پروفایل
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            // ذخیره در پوشه admin-profiles
            $user->profile_photo_path = $request->file('profile_photo')->store('admin-profiles', 'public');
        }

        // آپلود بنر داشبورد
        if ($request->hasFile('dashboard_banner')) {
            if ($user->dashboard_banner_path) {
                Storage::disk('public')->delete($user->dashboard_banner_path);
            }
            // ذخیره در پوشه admin-banners
            $user->dashboard_banner_path = $request->file('dashboard_banner')->store('admin-banners', 'public');
        }

        $user->save();

        return back()->with('success', 'پروفایل با موفقیت بروزرسانی شد.');
    }
}