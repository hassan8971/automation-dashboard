@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 text-right shadow-sm">
        <strong class="font-bold">خطا!</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">نام افزونه</label>
            <input type="text" name="name" value="{{ old('name', $addon->name ?? '') }}" 
                   class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white" required>
        </div>

        {{-- قیمت با فرمت ۳ رقمی (AlpineJS) --}}
        <div x-data="{ 
            rawPrice: '{{ old('price', $addon->price ?? '') }}',
            format(value) {
                if (!value) return '';
                return value.toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            },
            update(event) {
                let val = event.target.value.replace(/[^0-9]/g, '');
                this.rawPrice = val;
                event.target.value = this.format(val);
            }
        }">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">قیمت (تومان)</label>
            
            <input 
                type="text" 
                inputmode="numeric" 
                class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white text-left font-mono"
                :value="format(rawPrice)"
                @input="update($event)"
                placeholder="مثال: 50,000"
            >
            <input type="hidden" name="price" :value="rawPrice">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">مدت اعتبار (روز)</label>
            <input type="number" name="duration_in_days" value="{{ old('duration_in_days', $addon->duration_in_days ?? 30) }}" 
                   class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white" required>
        </div>

        {{-- انتخاب هدیه --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">انتخاب هدیه (اختیاری)</label>
            <select name="gift_id" class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white">
                <option value="">-- بدون هدیه --</option>
                @foreach($gifts as $g)
                    <option value="{{ $g->id }}" @selected(old('gift_id', $addon->gift_id ?? '') == $g->id)>
                        {{ $g->title }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-right mb-2">توضیحات</label>
        <textarea name="description" rows="3" 
                  class="block w-full px-4 py-2.5 border rounded-lg dark:bg-dark-paper dark:border-gray-600 dark:text-white">{{ old('description', $addon->description ?? '') }}</textarea>
    </div>

    <div class="flex items-center">
        <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $addon->is_active ?? true)) class="w-4 h-4 text-purple-600 rounded">
        <label for="is_active" class="mr-3 block text-sm font-semibold text-gray-700 dark:text-gray-300">این افزونه فعال باشد</label>
    </div>

    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md">
            ذخیره اطلاعات
        </button>
    </div>
</div>