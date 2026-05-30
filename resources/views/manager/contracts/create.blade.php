<x-app-layout>
    <x-slot name="title">{{ __('Contracts') }} — {{ __('Add') }}</x-slot>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ __('Add') }} — {{ __('Contracts') }}</h2>
        <a href="{{ route('manager.contracts.index') }}" class="text-sm text-gray-600">{{ __('Back') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ route('manager.contracts.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Employees') }}</label>
                <select name="employee_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">--</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employee_id')==$emp->id)>{{ $emp->name }} ({{ $emp->email }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Type') }}</label>
                    <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="employment" @selected(old('type','employment')==='employment')>{{ __('Employment') }}</option>
                        <option value="service"    @selected(old('type')==='service')>{{ __('Service') }}</option>
                        <option value="freelance"  @selected(old('type')==='freelance')>{{ __('Freelance') }}</option>
                        <option value="supplier"   @selected(old('type')==='supplier')>{{ __('Supplier') }}</option>
                        <option value="other"      @selected(old('type')==='other')>{{ __('Other') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="draft"      @selected(old('status','draft')==='draft')>{{ __('Draft') }}</option>
                        <option value="active"     @selected(old('status')==='active')>{{ __('Active') }}</option>
                        <option value="expired"    @selected(old('status')==='expired')>{{ __('Expired') }}</option>
                        <option value="terminated" @selected(old('status')==='terminated')>{{ __('Terminated') }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start Date') }}</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Date') }}</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Value') }} ({{ __('Optional') }})</label>
                <input type="number" step="0.01" name="value" value="{{ old('value') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Document') }} (PDF/DOC — {{ __('Optional') }})</label>
                <input type="file" name="document" accept=".pdf,.doc,.docx" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-2 pt-3">
                <button class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">{{ __('Save') }}</button>
                <a href="{{ route('manager.contracts.index') }}" class="text-gray-600 px-3 py-2 text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
