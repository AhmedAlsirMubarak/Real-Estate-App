<x-app-layout>
    <x-slot name="title">{{ __('Owners Association') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ __('Owners Association') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Associations') }}</p>
        </div>
        <a href="{{ route('manager.associations.create') }}"
           class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('Add') }}
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ __('Name') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Property') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Owners') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Monthly Fee per Unit') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($associations as $assoc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $assoc->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $assoc->property->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $assoc->property?->owners?->count() ?? 0 }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ number_format($assoc->monthly_fee_per_unit, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                                {{ $assoc->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $assoc->status === 'active' ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <a href="{{ route('manager.associations.show', $assoc) }}" class="text-blue-700 hover:text-blue-900 mx-1">{{ __('View Details') }}</a>
                            <a href="{{ route('manager.associations.edit', $assoc) }}" class="text-gray-600 hover:text-gray-900 mx-1">{{ __('Edit') }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-10 text-center text-gray-400">{{ __('No data available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $associations->links() }}</div>
    </div>
</x-app-layout>
