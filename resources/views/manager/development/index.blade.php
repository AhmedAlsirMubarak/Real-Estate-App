<x-app-layout>
    @php
        $isAr     = app()->getLocale() === 'ar';
        $tr       = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
        $catLabels = $isAr ? [
            'construction'       => 'أعمال البناء',
            'manpower'           => 'العمالة',
            'materials'          => 'المواد',
            'contractor_fees'    => 'رسوم المقاولين',
            'permits'            => 'التصاريح',
            'equipment_rental'   => 'إيجار المعدات',
            'design_engineering' => 'التصميم والهندسة',
            'utilities'          => 'المرافق',
        ] : [
            'construction'       => 'Construction',
            'manpower'           => 'Manpower',
            'materials'          => 'Materials',
            'contractor_fees'    => 'Contractor Fees',
            'permits'            => 'Permits',
            'equipment_rental'   => 'Equipment Rental',
            'design_engineering' => 'Design & Engineering',
            'utilities'          => 'Utilities',
        ];
    @endphp
    <x-slot name="title">{{ $tr('تطوير العقارات', 'Real Estate Development') }}</x-slot>

    <div class="py-4 space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">{{ $tr('مشاريع التطوير العقاري', 'Real Estate Development') }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $tr('تتبع مشاريع البناء والتطوير الجارية', 'Track construction and development projects') }}</p>
            </div>
            <a href="{{ route('manager.development.create') }}"
               class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('مشروع جديد', 'New Project') }}
            </a>
        </div>

        {{-- Summary KPIs --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('إجمالي المشاريع', 'Total Projects') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $projects->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('مشاريع نشطة', 'Active Projects') }}</p>
                <p class="text-2xl font-bold text-teal-600">{{ $activeCount }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('إجمالي الميزانيات', 'Total Budgets') }}</p>
                <p class="text-xl font-bold text-gray-800">{{ number_format($totalBudget) }} {{ $currency }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('إجمالي الصرف', 'Total Spent') }}</p>
                <p class="text-xl font-bold text-orange-600">{{ number_format($totalSpent) }} {{ $currency }}</p>
            </div>
        </div>

        {{-- Projects grid --}}
        @if($projects->isEmpty())
        <div class="bg-white rounded-xl shadow p-16 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <p class="text-gray-400 text-lg font-medium mb-2">{{ $tr('لا توجد مشاريع بعد', 'No projects yet') }}</p>
            <a href="{{ route('manager.development.create') }}" class="inline-flex items-center gap-1.5 text-teal-600 hover:text-teal-700 font-semibold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $tr('أنشئ أول مشروع', 'Create your first project') }}
            </a>
        </div>
        @else
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($projects as $project)
            @php
                $pIdx       = $project->phaseIndex();
                $phases5    = ['planning', 'foundation', 'structure', 'finishing', 'handover'];
                $bPct       = $project->budgetUsedPercent();
                $barColor   = $bPct >= 90 ? 'bg-red-500' : ($bPct >= 70 ? 'bg-yellow-500' : 'bg-teal-500');
                $days       = $project->daysToCompletion();
                $typeColors = ['residential' => 'bg-blue-100 text-blue-700', 'commercial' => 'bg-purple-100 text-purple-700', 'mixed' => 'bg-indigo-100 text-indigo-700'];
                $statusColors = ['planning' => 'bg-gray-100 text-gray-600', 'foundation' => 'bg-orange-100 text-orange-700', 'structure' => 'bg-yellow-100 text-yellow-700', 'finishing' => 'bg-blue-100 text-blue-700', 'handover' => 'bg-teal-100 text-teal-700', 'completed' => 'bg-green-100 text-green-700'];
                $topExp = $project->expenses->groupBy('category')->map(fn($g) => $g->sum('amount'))->sortDesc()->take(3);
                $tSpent = $project->totalSpent() ?: 1;
            @endphp
            <div class="bg-white rounded-xl shadow border border-gray-100 flex flex-col overflow-hidden">

                {{-- Card header --}}
                <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-gray-900 truncate">{{ $project->name }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $project->location }}
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $typeColors[$project->type] ?? 'bg-gray-100 text-gray-600' }}">{{ $project->typeLabel($isAr) }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $project->statusLabel($isAr) }}</span>
                    </div>
                </div>

                {{-- Phase progress --}}
                <div class="px-5 py-3 border-b border-gray-50">
                    <div class="flex items-center">
                        @foreach($phases5 as $i => $phase)
                        @php $thisIdx = $i; @endphp
                        <div class="flex items-center flex-1">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs flex-shrink-0 font-bold
                                {{ $pIdx > $thisIdx ? 'bg-teal-500 text-white' : ($pIdx === $thisIdx ? 'bg-blue-600 text-white ring-2 ring-blue-200' : 'bg-gray-100 text-gray-400') }}">
                                @if($pIdx > $thisIdx)
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @else
                                {{ $i + 1 }}
                                @endif
                            </div>
                            @if($i < 4)
                            <div class="flex-1 h-0.5 mx-0.5 {{ $pIdx > $thisIdx ? 'bg-teal-400' : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-xs text-gray-400 mt-1.5">
                        <span>{{ $tr('تخطيط', 'Planning') }}</span>
                        <span>{{ $tr('تسليم', 'Handover') }}</span>
                    </div>
                </div>

                {{-- Budget bar --}}
                <div class="px-5 py-3 border-b border-gray-50">
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-gray-500">{{ $tr('الميزانية المستخدمة', 'Budget Used') }}</span>
                        <span class="font-semibold text-gray-700">{{ $bPct }}%</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $barColor }} rounded-full transition-all" style="width: {{ $bPct }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400 mt-1.5">
                        <span>{{ $tr('إجمالي:', 'Total:') }} {{ number_format($project->total_budget) }} {{ $currency }}</span>
                        <span>{{ $tr('صُرف:', 'Spent:') }} {{ number_format($project->totalSpent()) }} {{ $currency }}</span>
                    </div>
                </div>

                {{-- Top expenses --}}
                @if($topExp->isNotEmpty())
                <div class="px-5 py-3 border-b border-gray-50">
                    <p class="text-xs text-gray-400 mb-2">{{ $tr('أبرز المصروفات', 'Top Expenses') }}</p>
                    <div class="space-y-1.5">
                        @foreach($topExp as $cat => $total)
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 w-28 truncate">{{ $catLabels[$cat] ?? $cat }}</span>
                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-400 rounded-full" style="width: {{ round(($total / $tSpent) * 100) }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 w-8 text-right">{{ round(($total / $tSpent) * 100) }}%</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Footer --}}
                <div class="px-5 py-3 flex items-center justify-between mt-auto">
                    <div class="text-xs text-gray-500 space-y-0.5">
                        <p><span class="font-semibold text-gray-700">{{ $project->progress_percentage }}%</span> {{ $tr('منجز', 'complete') }}</p>
                        <p class="{{ $days < 0 ? 'text-red-500 font-medium' : ($days < 60 ? 'text-yellow-600' : 'text-gray-400') }}">
                            {{ abs($days) }} {{ $tr('يوم', 'd') }}
                            {{ $days < 0 ? ($isAr ? 'تأخر' : 'overdue') : ($isAr ? 'متبقي' : 'left') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('manager.development.edit', $project) }}"
                           class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <a href="{{ route('manager.development.show', $project) }}"
                           class="bg-teal-600 hover:bg-teal-700 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                            {{ $tr('عرض', 'Open') }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</x-app-layout>
