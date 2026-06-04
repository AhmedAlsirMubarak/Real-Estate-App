<x-app-layout>
    <x-slot name="title">{{ __('My Meetings') }}</x-slot>

    <h2 class="text-xl font-bold text-gray-800 mb-5">{{ __('My Meetings') }}</h2>

    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                <tr>
                    <th class="px-4 py-3 text-right">{{ __('Title') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Property') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Scheduled At') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($meetings as $m)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $m->title }}</td>
                <td class="px-4 py-3 text-xs text-gray-600">{{ $m->association->property->name }}</td>
                <td class="px-4 py-3 text-xs">{{ $m->scheduled_at->format('Y/m/d H:i') }}</td>
                <td class="px-4 py-3 text-xs">{{ $m->statusLabel() }}</td>
                <td class="px-4 py-3 text-xs"><a href="{{ route('owner.meetings.show', $m) }}" class="text-blue-700">{{ __('View Details') }}</a></td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-10 text-center text-gray-400">{{ __('No data available') }}</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="p-3 border-t border-gray-100">{{ $meetings->links() }}</div>
    </div>
</x-app-layout>
