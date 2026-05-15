<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $sectionLabel = match($section) {
            'hoa'        => $tr('جمعية الملاك', 'Owners Association'),
            'management' => $tr('إدارة المباني', 'Building Management'),
            default      => $tr('كل التقارير المجدولة', 'All Scheduled Reports'),
        };
    @endphp
    <x-slot name="title">{{ $tr('تقارير مجدولة', 'Scheduled Reports') }} — {{ $sectionLabel }}</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ $sectionLabel }}</h2>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $tr('تتولد تلقائياً حسب الفترة المحددة', 'Auto-generated on the configured period') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('manager.scheduled-reports.index') }}"
                   class="text-xs px-3 py-1.5 rounded-lg border {{ !$section ? 'bg-slate-100 border-slate-300' : 'border-slate-200 hover:bg-slate-50' }}">
                    {{ $tr('الكل', 'All') }}
                </a>
                <a href="{{ route('manager.scheduled-reports.index', ['section' => 'hoa']) }}"
                   class="text-xs px-3 py-1.5 rounded-lg border {{ $section === 'hoa' ? 'bg-slate-100 border-slate-300' : 'border-slate-200 hover:bg-slate-50' }}">
                    {{ $tr('جمعية الملاك', 'HOA') }}
                </a>
                <a href="{{ route('manager.scheduled-reports.index', ['section' => 'management']) }}"
                   class="text-xs px-3 py-1.5 rounded-lg border {{ $section === 'management' ? 'bg-slate-100 border-slate-300' : 'border-slate-200 hover:bg-slate-50' }}">
                    {{ $tr('إدارة المباني', 'Building Mgmt') }}
                </a>
                <a href="{{ route('manager.scheduled-reports.create', ['section' => $section]) }}"
                   class="text-xs bg-blue-600 text-white hover:bg-blue-700 px-3 py-1.5 rounded-lg font-medium">
                    + {{ $tr('إضافة تقرير مجدول', 'New Scheduled Report') }}
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold">{{ $tr('الاسم', 'Name') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ $tr('القسم', 'Section') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ $tr('النطاق', 'Scope') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ $tr('الفترة', 'Period') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ $tr('التشغيل القادم', 'Next Run') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ $tr('الحالة', 'Status') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ $tr('إجراءات', 'Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $report->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $report->sectionLabel() }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($report->association)
                                    {{ $tr('جمعية:', 'Assoc:') }} {{ $report->association->name }}
                                @elseif($report->property)
                                    {{ $tr('عقار:', 'Property:') }} {{ $report->property->name }}
                                @else
                                    <span class="text-gray-400">{{ $tr('كل القسم', 'Whole section') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $report->period_months }} {{ $tr('شهر', 'mo.') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ optional($report->next_run_at)->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $report->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $report->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('manager.scheduled-reports.run', $report) }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">{{ $tr('تشغيل الآن', 'Run now') }}</button>
                                    </form>
                                    <a href="{{ route('manager.scheduled-reports.edit', $report) }}" class="text-xs text-gray-600 hover:text-gray-800">{{ $tr('تعديل', 'Edit') }}</a>
                                    <form method="POST" action="{{ route('manager.scheduled-reports.destroy', $report) }}"
                                          onsubmit="return confirm('{{ $tr('هل أنت متأكد؟', 'Are you sure?') }}');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800">{{ $tr('حذف', 'Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                {{ $tr('لا توجد تقارير مجدولة', 'No scheduled reports yet') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-3 border-t border-gray-100">
            {{ $reports->links() }}
        </div>
    </div>
</x-app-layout>
