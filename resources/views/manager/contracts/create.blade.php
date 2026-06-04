<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr   = fn(string $ar, string $en) => $isAr ? $ar : $en;
    @endphp
    <x-slot name="title">{{ $tr('العقود', 'Contracts') }} — {{ $tr('إضافة', 'Add') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ $tr('إضافة', 'Add') }} — {{ $tr('العقود', 'Contracts') }}</h2>
        <a href="{{ route('manager.contracts.index') }}" class="text-sm text-gray-600">{{ $tr('رجوع', 'Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ route('manager.contracts.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الموظفون', 'Employees') }}</label>
                <select name="employee_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">--</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employee_id')==$emp->id)>{{ $emp->name }} ({{ $emp->email }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('العنوان', 'Title') }}</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('النوع', 'Type') }}</label>
                    <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="employment" @selected(old('type','employment')==='employment')>{{ $tr('توظيف', 'Employment') }}</option>
                        <option value="service"    @selected(old('type')==='service')>{{ $tr('خدمات', 'Service') }}</option>
                        <option value="freelance"  @selected(old('type')==='freelance')>{{ $tr('عمل حر', 'Freelance') }}</option>
                        <option value="supplier"   @selected(old('type')==='supplier')>{{ $tr('مورّد', 'Supplier') }}</option>
                        <option value="other"      @selected(old('type')==='other')>{{ $tr('أخرى', 'Other') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الحالة', 'Status') }}</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="draft"      @selected(old('status','draft')==='draft')>{{ $tr('مسودة', 'Draft') }}</option>
                        <option value="active"     @selected(old('status')==='active')>{{ $tr('نشط', 'Active') }}</option>
                        <option value="expired"    @selected(old('status')==='expired')>{{ $tr('منتهي', 'Expired') }}</option>
                        <option value="terminated" @selected(old('status')==='terminated')>{{ $tr('مُنهى', 'Terminated') }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ البدء', 'Start Date') }}</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ الانتهاء', 'End Date') }}</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('القيمة', 'Value') }} ({{ $tr('اختياري', 'Optional') }})</label>
                <input type="number" step="0.01" name="value" value="{{ old('value') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المستند', 'Document') }} (PDF/DOC — {{ $tr('اختياري', 'Optional') }})</label>
                <input type="file" name="document" accept=".pdf,.doc,.docx" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات', 'Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ $tr('حفظ', 'Save') }}</button>
                <a href="{{ route('manager.contracts.index') }}" class="text-gray-600 px-3 py-2 text-sm">{{ $tr('إلغاء', 'Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
