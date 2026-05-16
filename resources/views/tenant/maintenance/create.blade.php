<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('تقديم طلب صيانة', 'Submit Maintenance Request') }}</x-slot>
    <div class="py-4 max-w-xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('tenant.maintenance.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $tr('تقديم طلب صيانة جديد', 'Submit New Maintenance Request') }}</h2>
        </div>

        @php $contract = auth()->user()->tenant->activeContract ?? null; @endphp
        @if($contract)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <p class="text-sm text-blue-700">
                <strong>{{ $tr('وحدتك الحالية:', 'Your current unit:') }}</strong>
                {{ $contract->unit->property->name ?? '' }} - {{ $tr('وحدة', 'Unit') }} {{ $contract->unit->unit_number ?? '' }}
            </p>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('tenant.maintenance.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('عنوان المشكلة', 'Issue Title') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="{{ $tr('مثال: تسرب في السقف...', 'Example: Roof leakage...') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-right focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('وصف المشكلة', 'Issue Description') }} <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required placeholder="{{ $tr('صف المشكلة بالتفصيل...', 'Describe the issue in detail...') }}"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-right focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('درجة الأولوية', 'Priority') }} <span class="text-red-500">*</span></label>
                    <select name="priority" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-right focus:ring-2 focus:ring-blue-500">
                        <option value="low" {{ old('priority')==='low'?'selected':'' }}>{{ $tr('منخفضة', 'Low') }}</option>
                        <option value="medium" {{ old('priority','medium')==='medium'?'selected':'' }}>{{ $tr('متوسطة', 'Medium') }}</option>
                        <option value="high" {{ old('priority')==='high'?'selected':'' }}>{{ $tr('عالية', 'High') }}</option>
                        <option value="urgent" {{ old('priority')==='urgent'?'selected':'' }}>{{ $tr('عاجلة', 'Urgent') }}</option>
                    </select>
                </div>

                {{-- Image upload --}}
                <div x-data="{
                    previews: [],
                    addFiles(event) {
                        const files = Array.from(event.target.files);
                        files.forEach(file => {
                            if (!file.type.startsWith('image/')) return;
                            const reader = new FileReader();
                            reader.onload = e => this.previews.push({ src: e.target.result, name: file.name });
                            reader.readAsDataURL(file);
                        });
                    },
                    remove(index) {
                        this.previews.splice(index, 1);
                        // rebuild the file input — simplest way is to clear & re-add remaining
                        // previews are visual only; actual removal handled by clearing input
                    }
                }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $tr('صور المشكلة', 'Issue Photos') }}
                        <span class="text-gray-400 font-normal text-xs">{{ $tr('(اختياري — حتى 10 صور)', '(optional — up to 10 images)') }}</span>
                    </label>

                    @error('images')<p class="text-red-500 text-xs mb-1">{{ $message }}</p>@enderror
                    @error('images.*')<p class="text-red-500 text-xs mb-1">{{ $message }}</p>@enderror

                    <label class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 hover:border-blue-400 hover:bg-gray-50 rounded-xl p-6 cursor-pointer transition">
                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">{{ $tr('انقر لإضافة صور أو اسحبها هنا', 'Click to add photos or drag them here') }}</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — {{ $tr('بحد أقصى 5 ميجا للصورة', 'max 5 MB each') }}</p>
                        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="hidden" @change="addFiles($event)">
                    </label>

                    <div x-show="previews.length > 0" class="grid grid-cols-3 gap-2 mt-3">
                        <template x-for="(img, i) in previews" :key="i">
                            <div class="relative group rounded-lg overflow-hidden border border-gray-200 aspect-square">
                                <img :src="img.src" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <button type="button" @click="remove(i)"
                                            class="bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold leading-none">×</button>
                                </div>
                                <p class="absolute bottom-0 inset-x-0 bg-black/50 text-white text-xs px-1 py-0.5 truncate" x-text="img.name"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('إرسال الطلب', 'Submit Request') }}</button>
                    <a href="{{ route('tenant.maintenance.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-medium transition">{{ $tr('إلغاء', 'Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
