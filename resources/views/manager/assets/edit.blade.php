<x-app-layout>
    <x-slot name="title">{{ __('Company Assets') }} — {{ __('Edit') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Edit') }} — {{ $asset->name }}</h2>
        <a href="{{ route('manager.assets.index') }}" class="text-sm text-gray-600">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ route('manager.assets.update', $asset) }}" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ old('name', $asset->name) }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Category') }}</label>
                    <select name="category" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="laptop"           @selected(old('category',$asset->category)==='laptop')>{{ __('Laptops') }}</option>
                        <option value="mobile"           @selected(old('category',$asset->category)==='mobile')>{{ __('Mobiles') }}</option>
                        <option value="office_equipment" @selected(old('category',$asset->category)==='office_equipment')>{{ __('Office Equipment') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Serial Number') }} ({{ __('Optional') }})</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="available"    @selected(old('status',$asset->status)==='available')>{{ __('Available') }}</option>
                        <option value="assigned"     @selected(old('status',$asset->status)==='assigned')>{{ __('Assigned') }}</option>
                        <option value="under_repair" @selected(old('status',$asset->status)==='under_repair')>{{ __('Under Repair') }}</option>
                        <option value="retired"      @selected(old('status',$asset->status)==='retired')>{{ __('Retired') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Assigned To') }} ({{ __('Optional') }})</label>
                    <select name="assigned_to" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">— {{ __('Unassigned') }} —</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('assigned_to',$asset->assigned_to)==$emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Purchase Date') }} ({{ __('Optional') }})</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Purchase Price') }} ({{ __('Optional') }})</label>
                    <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', $asset->purchase_price) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('notes', $asset->notes) }}</textarea>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ __('Update') }}</button>
                <a href="{{ route('manager.assets.index') }}" class="text-gray-600 px-3 py-2 text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
