<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Gift;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AddonController extends Controller
{
    public function index()
    {
        $addons = Addon::with('gift')->latest()->get();
        return view('admin.addons.index', compact('addons'));
    }

    public function create()
    {
        $gifts = Gift::where('is_active', true)->get();
        return view('admin.addons.create', compact('gifts'));
    }

    public function store(Request $request)
    {
        $this->saveAddon($request, new Addon());
        return redirect()->route('admin.addons.index')->with('success', 'افزونه با موفقیت ایجاد شد.');
    }

    public function edit(Addon $addon)
    {
        $gifts = Gift::where('is_active', true)->get();
        return view('admin.addons.edit', compact('addon', 'gifts'));
    }

    public function update(Request $request, Addon $addon)
    {
        $this->saveAddon($request, $addon);
        return redirect()->route('admin.addons.index')->with('success', 'افزونه ویرایش شد.');
    }

    public function destroy(Addon $addon)
    {
        $addon->delete();
        return redirect()->route('admin.addons.index')->with('success', 'افزونه حذف شد.');
    }

    private function saveAddon(Request $request, Addon $addon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            // 'supported_apps' حذف شد
            'description' => 'nullable|string',
            'gift_id' => 'nullable|exists:gifts,id',
            'is_active' => 'nullable|boolean',
        ]);

        $addon->name = $validated['name'];
        if (!$addon->exists || $addon->isDirty('name')) {
            $addon->slug = Str::slug($validated['name']);
        }
        $addon->price = $validated['price'];
        $addon->duration_in_days = $validated['duration_in_days'];
        $addon->description = $validated['description'];
        $addon->gift_id = $validated['gift_id'];
        $addon->is_active = $request->has('is_active');

        $addon->save();
    }
}