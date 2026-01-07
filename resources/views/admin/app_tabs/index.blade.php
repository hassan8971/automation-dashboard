@extends('admin.layouts.app')

@section('title', 'مدیریت تب‌های اپلیکیشن')

@section('content')
<div class="card bg-white dark:bg-dark-paper shadow-sm rounded-lg border dark:border-gray-700">
    <div class="card-header px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center">
        <div>
            <h4 class="text-lg font-bold text-gray-800 dark:text-white">تب‌های پایین اپلیکیشن (Bottom Navigation)</h4>
            <p class="text-xs text-gray-500 mt-1">این دکمه‌ها در تمام صفحات اپلیکیشن ثابت هستند.</p>
        </div>
        <a href="{{ route('admin.app-tabs.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow transition flex items-center gap-2">
            <i class="fas fa-plus"></i> تب جدید
        </a>
    </div>
    
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 dark:bg-dark-hover text-gray-500 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3">ترتیب</th>
                        <th class="px-6 py-3">آیکون</th>
                        <th class="px-6 py-3">عنوان</th>
                        <th class="px-6 py-3">لینک (Slug)</th>
                        <th class="px-6 py-3">وضعیت</th>
                        <th class="px-6 py-3 text-left">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($tabs as $tab)
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition">
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-mono">{{ $tab->sort_order }}</td>
                        <td class="px-6 py-4">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-dark-hover flex items-center justify-center text-xl text-blue-600 overflow-hidden">
                                @if($tab->image_path)
                                    <img src="{{ asset('storage/' . $tab->image_path) }}" class="w-full h-full object-contain p-1">
                                @else
                                    <i class="{{ $tab->icon ?? 'fas fa-circle' }}"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-800 dark:text-white">{{ $tab->title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 dir-ltr text-right font-mono bg-gray-50 dark:bg-dark-hover rounded inline-block px-2 py-1 mx-6 my-2">{{ $tab->link }}</td>
                        <td class="px-6 py-4">
                            @if($tab->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">فعال</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">غیرفعال</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-left">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.app-tabs.edit', $tab->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition" title="ویرایش">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.app-tabs.destroy', $tab->id) }}" method="POST" onsubmit="return confirm('حذف شود؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="حذف">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            هنوز تبی ساخته نشده است.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection