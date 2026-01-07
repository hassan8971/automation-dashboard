<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppTab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppTabController extends Controller
{
    /**
     * نمایش لیست تب‌ها
     */
    public function index()
    {
        $tabs = AppTab::orderBy('sort_order')->get();
        return view('admin.app_tabs.index', compact('tabs'));
    }

    /**
     * فرم ایجاد تب جدید
     */
    public function create()
    {
        return view('admin.app_tabs.create');
    }

    /**
     * ذخیره در دیتابیس
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:1024',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        // اگر ترتیب وارد نشد، به انتهای لیست اضافه شود
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = AppTab::max('sort_order') + 1;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('tab_icons', 'public');
        }

        // هندل کردن چک‌باکس (اگر تیک نخورده باشد در ریکوئست نمیاید)
        $data['is_active'] = $request->has('is_active');

        AppTab::create($data);

        return redirect()->route('admin.app-tabs.index')
            ->with('success', 'تب جدید با موفقیت ساخته شد.');
    }

    /**
     * فرم ویرایش
     */
    public function edit(AppTab $appTab)
    {
        return view('admin.app_tabs.edit', compact('appTab'));
    }

    /**
     * آپدیت در دیتابیس
     */
    public function update(Request $request, AppTab $appTab)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:1024',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // حذف عکس قبلی اگر وجود دارد
            if ($appTab->image_path) {
                Storage::disk('public')->delete($appTab->image_path);
            }
            $data['image_path'] = $request->file('image')->store('tab_icons', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        $appTab->update($data);

        return redirect()->route('admin.app-tabs.index')
            ->with('success', 'تب مورد نظر ویرایش شد.');
    }

    /**
     * حذف تب
     */
    public function destroy(AppTab $appTab)
    {
        if ($appTab->image_path) {
            Storage::disk('public')->delete($appTab->image_path);
        }

        $appTab->delete();
        return back()->with('success', 'تب حذف شد.');
    }
}