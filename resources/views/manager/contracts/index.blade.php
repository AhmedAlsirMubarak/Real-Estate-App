<x-app-layout>
    <x-slot name="title">{{ __('Contracts') }}</x-slot>

    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ __('Contracts') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $contracts->total() }} {{ __('Total') }}</p>
        </div>
        <a href="{{ route('manager.contracts.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">{{ __('Add') }}</a>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-5 grid grid-cols-1 md:grid-cols-4 gap-3">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Status') }}</option>
            <option value="draft"      @selected(request('status')==='draft')>{{ __('Draft') }}</option>
            <option value="active"     @selected(request('status')==='active')>{{ __('Active') }}</option>
            <option value="expired"    @selected(request('status')==='expired')>{{ __('Expired') }}</option>
            <option value="terminated" @selected(request('status')==='terminated')>{{ __('Terminated') }}</option>
        </select>
        <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Type') }}</option>
            <option value="employment" @selected(request('type')==='employment')>{{ __('Employment') }}</option>
            <option value="service"    @selected(request('type')==='service')>{{ __('Service') }}</option>
            <option value="freelance"  @selected(request('type')==='freelance')>{{ __('Freelance') }}</option>
            <option value="supplier"   @selected(request('type')==='supplier')>{{ __('Supplier') }}</option>
            <option value="other"      @selected(request('type')==='other')>{{ __('Other') }}</option>
        </select>
        <select name="employee_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('Employees') }}</option>
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}" @selected(request('employee_id')==$emp->id)>{{ $emp->name }}</option>
            @endforeach
        </select>
        <button class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm">{{ __('Search') }}</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">{{ __('Title') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Employees') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Type') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Start Date') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('End Date') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Value') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Document') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contracts as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $c->title }}</td>
                        <td class="px-4 py-3">{{ $c->employee?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $c->typeLabel() }}</td>
                        <td class="px-4 py-3 text-xs">{{ $c->start_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-xs">{{ $c->end_date?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $c->value ? number_format($c->value, 2) : '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($c->status==='active')     bg-green-50 text-green-700
                                @elseif($c->status==='draft')   bg-gray-100 text-gray-600
                                @elseif($c->status==='expired') bg-yellow-50 text-yellow-700
                                @else bg-red-50 text-red-700 @endif">
                                {{ $c->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($c->document_path)
                            <div class="flex gap-2 items-center flex-wrap">
                                <a href="{{ asset('storage/' . $c->document_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-700 hover:bg-blue-100 px-2 py-1 rounded-md font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ __('Preview') }}
                                </a>
                                <a href="{{ asset('storage/' . $c->document_path) }}" download
                                   class="inline-flex items-center gap-1 text-xs bg-gray-50 text-gray-700 hover:bg-gray-100 px-2 py-1 rounded-md font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    {{ __('Download') }}
                                </a>
                            </div>
                            @else
                            <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2 flex-wrap items-center">
                                <a href="{{ route('manager.contracts.edit', $c) }}" class="text-indigo-600 hover:text-indigo-800 text-xs">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('manager.contracts.destroy', $c) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-xs">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="py-10 text-center text-gray-400">{{ __('No data available') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $contracts->links() }}</div>
    </div>
</x-app-layout>
