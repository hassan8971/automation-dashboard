<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AddonController extends Controller
{
    public function index()
    {
        $addons = Addon::latest()->get();
        return view('admin.addons.index', compact('addons'));
    }

    public function create()
    {
        return view('admin.addons.create');
    }

    public function store(Request $request)
    {
        $this->saveAddon($request, new Addon());
        return redirect()->route('admin.addons.index')->with('success', 'افزونه (Add-on) با موفقیت ایجاد شد.');
    }

    public function edit(Addon $addon)
    {
        return view('admin.addons.edit', compact('addon'));
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
            'supported_apps' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $addon->name = $validated['name'];
        $addon->slug = Str::slug($validated['name']); // Or handle slug uniqueness explicitly if needed
        $addon->price = $validated['price'];
        $addon->duration_in_days = $validated['duration_in_days'];
        $addon->description = $validated['description'];
        $addon->is_active = $request->has('is_active');

        // Handle Array Conversion
        if (!empty($validated['supported_apps'])) {
            $apps = array_map('trim', explode(',', $validated['supported_apps']));
            $addon->supported_apps = $apps;
        } else {
            $addon->supported_apps = [];
        }

        $addon->save();
    }
}