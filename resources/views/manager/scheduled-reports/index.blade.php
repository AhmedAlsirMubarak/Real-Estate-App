<x-app-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $sectionLabel = $tr('تقارير إدارة المباني المجدولة', 'Building Management Scheduled Reports');
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
                <a href="{{ route('manager.associations.report.create') }}"
                   class="text-xs bg-teal-600 text-white hover:bg-teal-700 px-3 py-1.5 rounded-lg font-medium inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                    {{ $tr('تقرير HOA الشامل', 'HOA Comprehensive Report') }}
                </a>
                <a href="{{ route('manager.scheduled-reports.create', ['section' => 'management']) }}"
                   class="text-xs bg-blue-600 text-white hover:bg-blue-700 px-3 py-1.5 rounded-lg font-medium">
                    + {{ $tr('تقرير مجدول جديد', 'New Scheduled Report') }}
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
                        <th class="px-4 py-3 text-right font-semibold">{{ $tr('آخر تشغيل', 'Last Run') }}</th>
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
                                @php $lastRun = $report->latestRun; @endphp
                                @if($lastRun && $lastRun->status === 'success')
                                    <div class="text-xs text-gray-500">{{ optional($lastRun->generated_at)->format('Y-m-d H:i') }}</div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <a href="{{ route('manager.scheduled-reports.download', $lastRun) }}?preview=1" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs text-gray-600 hover:text-gray-900 font-medium">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            {{ $tr('عرض PDF', 'View PDF') }}
                                        </a>
                                        <a href="{{ route('manager.scheduled-reports.download', $lastRun) }}"
                                           class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            {{ $tr('تحميل PDF', 'Download PDF') }}
                                        </a>
                                    </div>
                                @elseif($lastRun && $lastRun->status === 'failed')
                                    <span class="text-xs text-red-500">{{ $tr('فشل التشغيل', 'Run failed') }}</span>
                                @else
                                    <span class="text-xs text-gray-400">{{ $tr('لم يُشغَّل بعد', 'Not run yet') }}</span>
                                @endif
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
