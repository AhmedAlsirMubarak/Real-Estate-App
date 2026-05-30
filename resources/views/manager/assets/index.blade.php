<x-app-layout>
    <x-slot name="title">{{ __('Company Assets') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ __('Company Assets') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $assets->total() }} {{ __('Total') }}</p>
        </div>
        <a href="{{ route('manager.assets.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">{{ __('Add') }}</a>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-5 grid grid-cols-1 md:grid-cols-4 gap-3">
        <select name="category" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Category') }}</option>
            <option value="laptop"           @selected(request('category')==='laptop')>{{ __('Laptops') }}</option>
            <option value="mobile"           @selected(request('category')==='mobile')>{{ __('Mobiles') }}</option>
            <option value="office_equipment" @selected(request('category')==='office_equipment')>{{ __('Office Equipment') }}</option>
        </select>
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Status') }}</option>
            <option value="available"    @selected(request('status')==='available')>{{ __('Available') }}</option>
            <option value="assigned"     @selected(request('status')==='assigned')>{{ __('Assigned') }}</option>
            <option value="under_repair" @selected(request('status')==='under_repair')>{{ __('Under Repair') }}</option>
            <option value="retired"      @selected(request('status')==='retired')>{{ __('Retired') }}</option>
        </select>
        <select name="assigned_to" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Employees') }}</option>
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}" @selected(request('assigned_to')==$emp->id)>{{ $emp->name }}</option>
            @endforeach
        </select>
        <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm">{{ __('Search') }}</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ __('Name') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Serial') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Category') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Assigned To') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Purchase Date') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Price') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assets as $a)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $a->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $a->serial_number ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $a->categoryLabel() }}</td>
                        <td class="px-4 py-3">{{ $a->assignedEmployee?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs">{{ $a->purchase_date?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $a->purchase_price ? number_format($a->purchase_price, 2) : '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($a->status==='available')    bg-green-50 text-green-700
                                @elseif($a->status==='assigned')  bg-blue-50 text-blue-700
                                @elseif($a->status==='under_repair') bg-yellow-50 text-yellow-700
                                @else bg-gray-100 text-gray-500 @endif">
                                {{ $a->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2 flex-wrap">
                                <a href="{{ route('manager.assets.edit', $a) }}" class="text-indigo-600 hover:text-indigo-800 text-xs">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('manager.assets.destroy', $a) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-xs">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-10 text-center text-gray-400">{{ __('No data available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $assets->links() }}</div>
    </div>
</x-app-layout>
