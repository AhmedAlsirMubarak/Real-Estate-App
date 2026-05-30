<x-app-layout>
    @php
        $isAr     = app()->getLocale() === 'ar';
        $tr       = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';

        $totalSpent  = $development->totalSpent();
        $remaining   = $development->remaining();
        $bPct        = $development->budgetUsedPercent();
        $days        = $development->daysToCompletion();
        $health      = $development->budgetHealth();
        $phases      = \App\Models\DevelopmentProject::phases();
        $pIdx        = $development->phaseIndex();

        $typeColors   = ['residential' => 'bg-blue-100 text-blue-700', 'commercial' => 'bg-purple-100 text-purple-700', 'mixed' => 'bg-indigo-100 text-indigo-700'];
        $statusColors = ['planning' => 'bg-gray-100 text-gray-600', 'foundation' => 'bg-orange-100 text-orange-700', 'structure' => 'bg-yellow-100 text-yellow-700', 'finishing' => 'bg-blue-100 text-blue-700', 'handover' => 'bg-teal-100 text-teal-700', 'completed' => 'bg-green-100 text-green-700'];
        $phaseLabels  = $isAr
            ? ['planning' => 'تخطيط', 'foundation' => 'أساسات', 'structure' => 'هيكل', 'finishing' => 'تشطيب', 'handover' => 'تسليم', 'completed' => 'مكتمل']
            : ['planning' => 'Planning', 'foundation' => 'Foundation', 'structure' => 'Structure', 'finishing' => 'Finishing', 'handover' => 'Handover', 'completed' => 'Completed'];
    @endphp
    <x-slot name="title">{{ $development->name }}</x-slot>

    <div class="py-4 space-y-6">

        {{-- Project header --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <a href="{{ route('manager.development.index') }}" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $typeColors[$development->type] ?? '' }}">{{ $development->typeLabel($isAr) }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $statusColors[$development->status] ?? '' }}">{{ $development->statusLabel($isAr) }}</span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-gray-900">{{ $development->name }}</h2>
                    <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $development->location }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $development->project_manager_name }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $development->start_date?->format('d M Y') }} → {{ $development->estimated_completion_date?->format('d M Y') }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
                    {{-- Update Progress modal trigger --}}
                    <div x-data="{ open: false }">
                        <button @click="open = true"
                                class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-2 rounded-lg font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            {{ $tr('تحديث التقدم', 'Update Progress') }}
                        </button>
                        <div x-show="open" x-transition class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="open = false">
                            <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6">
                                <h3 class="font-bold text-gray-900 mb-4">{{ $tr('تحديث التقدم', 'Update Progress') }}</h3>
                                <form method="POST" action="{{ route('manager.development.progress', $development) }}">
                                    @csrf @method('PATCH')
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المرحلة', 'Phase') }}</label>
                                            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                                @foreach($phases as $phase)
                                                <option value="{{ $phase }}" @selected($development->status === $phase)>{{ $phaseLabels[$phase] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نسبة الإنجاز:', 'Progress:') }} <span x-ref="pLabel">{{ $development->progress_percentage }}</span>%</label>
                                            <input type="range" name="progress_percentage"
                                                   value="{{ $development->progress_percentage }}"
                                                   min="0" max="100" step="1"
                                                   class="w-full accent-teal-600"
                                                   @input="$refs.pLabel.textContent = $event.target.value">
                                        </div>
                                    </div>
                                    <div class="flex gap-2 mt-5">
                                        <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-lg text-sm font-semibold transition">{{ $tr('حفظ', 'Save') }}</button>
                                        <button type="button" @click="open = false" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg text-sm font-semibold transition">{{ $tr('إلغاء', 'Cancel') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('manager.development.report', $development) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-3 py-2 rounded-lg font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ $tr('تقرير', 'Report') }}
                    </a>
                    <a href="{{ route('manager.development.edit', $development) }}"
                       class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-3 py-2 rounded-lg font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        {{ $tr('تعديل', 'Edit') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- KPI cards --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('إجمالي الميزانية', 'Total Budget') }}</p>
                <p class="text-lg font-bold text-gray-800">{{ number_format($development->total_budget) }}</p>
                <p class="text-xs text-gray-400">{{ $currency }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('المصروف', 'Spent') }}</p>
                <p class="text-lg font-bold text-orange-600">{{ number_format($totalSpent) }}</p>
                <p class="text-xs text-gray-400">{{ $currency }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('المتبقي', 'Remaining') }}</p>
                <p class="text-lg font-bold text-teal-600">{{ number_format($remaining) }}</p>
                <p class="text-xs text-gray-400">{{ $currency }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('مصروف هذا الشهر', 'This Month') }}</p>
                <p class="text-lg font-bold text-blue-600">{{ number_format($thisMonthSpending) }}</p>
                <p class="text-xs text-gray-400">{{ $currency }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('الإنجاز', 'Progress') }}</p>
                <p class="text-lg font-bold text-indigo-600">{{ $development->progress_percentage }}%</p>
                <div class="h-1.5 bg-gray-100 rounded-full mt-1.5 overflow-hidden"><div class="h-full bg-indigo-500 rounded-full" style="width:{{ $development->progress_percentage }}%"></div></div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">{{ $tr('الأيام المتبقية', 'Days Left') }}</p>
                <p class="text-lg font-bold {{ $days < 0 ? 'text-red-600' : ($days < 60 ? 'text-yellow-600' : 'text-gray-800') }}">{{ abs($days) }}</p>
                <p class="text-xs {{ $days < 0 ? 'text-red-400' : 'text-gray-400' }}">{{ $days < 0 ? $tr('تأخر','overdue') : $tr('يوم','days') }}</p>
            </div>
        </div>

        {{-- Phase timeline --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-bold text-gray-800 mb-5">{{ $tr('مراحل المشروع', 'Project Phases') }}</h3>
            <div class="flex items-center">
                @foreach($phases as $i => $phase)
                @php
                    $done    = $pIdx > $i;
                    $current = $pIdx === $i;
                    $pending = $pIdx < $i;
                @endphp
                <div class="flex items-center flex-1">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                            {{ $done ? 'bg-teal-500 text-white' : ($current ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-gray-100 text-gray-400') }}">
                            @if($done)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                            {{ $i + 1 }}
                            @endif
                        </div>
                        <span class="text-xs mt-1.5 font-medium {{ $done ? 'text-teal-600' : ($current ? 'text-blue-600' : 'text-gray-400') }} whitespace-nowrap">
                            {{ $phaseLabels[$phase] }}
                        </span>
                    </div>
                    @if($i < count($phases) - 1)
                    <div class="flex-1 h-0.5 mx-2 mb-5 {{ $done ? 'bg-teal-400' : 'bg-gray-200' }}"></div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Chart + Budget health --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Monthly chart --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4">{{ $tr('الصرف الشهري مقابل المخطط', 'Monthly Spend vs Expected') }}</h3>
                <canvas id="devMonthlyChart" height="120"></canvas>
            </div>

            {{-- Budget health + notes --}}
            <div class="space-y-4">
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-bold text-gray-800 mb-3">{{ $tr('حالة الميزانية', 'Budget Health') }}</h3>
                    @php
                        $healthConfig = [
                            'on_track'   => ['icon' => '✅', 'color' => 'bg-green-50 border-green-200', 'textColor' => 'text-green-700', 'label' => $tr('على المسار الصحيح', 'On Track')],
                            'at_risk'    => ['icon' => '⚠️', 'color' => 'bg-yellow-50 border-yellow-200', 'textColor' => 'text-yellow-700', 'label' => $tr('في خطر', 'At Risk')],
                            'over_budget'=> ['icon' => '🔴', 'color' => 'bg-red-50 border-red-200', 'textColor' => 'text-red-700', 'label' => $tr('تجاوز الميزانية', 'Over Budget')],
                        ];
                        $hc = $healthConfig[$health];
                    @endphp
                    <div class="rounded-lg border {{ $hc['color'] }} p-4 text-center">
                        <p class="text-3xl mb-1">{{ $hc['icon'] }}</p>
                        <p class="font-bold {{ $hc['textColor'] }} text-lg">{{ $hc['label'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $tr('الصرف', 'Spent') }}: {{ $bPct }}% · {{ $tr('الإنجاز', 'Progress') }}: {{ $development->progress_percentage }}%</p>
                    </div>
                    <div class="mt-3 h-2 bg-gray-100 rounded-full overflow-hidden">
                        @php $bBarColor = $bPct >= 90 ? 'bg-red-500' : ($bPct >= 70 ? 'bg-yellow-500' : 'bg-teal-500'); @endphp
                        <div class="h-full {{ $bBarColor }} rounded-full" style="width: {{ $bPct }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 text-center mt-1">{{ $bPct }}% {{ $tr('من الميزانية مُصروف', 'of budget spent') }}</p>
                </div>

                @if($development->notes)
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-semibold text-gray-700 mb-2 text-sm">{{ $tr('ملاحظات', 'Notes') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $development->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Category breakdown table --}}
        <div class="bg-white rounded-xl shadow">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">{{ $tr('تفصيل الميزانية بالفئات', 'Budget Breakdown by Category') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الفئة', 'Category') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الميزانية المخصصة', 'Allocated') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('المصروف الفعلي', 'Actual Spent') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('الفارق', 'Variance') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ $tr('% من المصروف', '% of Spent') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php $catLabelsAll = \App\Models\DevelopmentExpense::categoryLabels($isAr); @endphp
                        @foreach($categories as $cat)
                        @php
                            $allocated = $development->categoryBudget($cat);
                            $spent     = (float) ($expenseByCategory->get($cat)?->total ?? 0);
                            $variance  = $allocated > 0 ? $allocated - $spent : null;
                            $pct       = $totalSpent > 0 ? round(($spent / $totalSpent) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $catLabelsAll[$cat] }}</td>
                            <td class="px-4 py-3 text-gray-500">
                                @if($allocated > 0)
                                    {{ number_format($allocated) }} {{ $currency }}
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold {{ $spent > 0 ? 'text-orange-600' : 'text-gray-300' }}">
                                {{ $spent > 0 ? number_format($spent) . ' ' . $currency : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($variance !== null)
                                    <span class="{{ $variance >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                        {{ $variance >= 0 ? '+' : '' }}{{ number_format($variance) }} {{ $currency }}
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($pct > 0)
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-400 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 w-8 text-right">{{ $pct }}%</span>
                                </div>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-50 font-bold">
                            <td class="px-4 py-3 text-gray-800">{{ $tr('الإجمالي', 'Total') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ number_format($development->total_budget) }} {{ $currency }}</td>
                            <td class="px-4 py-3 text-orange-600">{{ number_format($totalSpent) }} {{ $currency }}</td>
                            <td class="px-4 py-3 {{ ($development->total_budget - $totalSpent) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($development->total_budget - $totalSpent) }} {{ $currency }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Contractors + Expense Log (2 columns) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Contractors --}}
            <div class="bg-white rounded-xl shadow flex flex-col" x-data="{ addContractor: false }">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">{{ $tr('المقاولون', 'Contractors') }}</h3>
                    <button @click="addContractor = !addContractor"
                            class="text-xs bg-teal-50 text-teal-700 hover:bg-teal-100 px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ $tr('إضافة مقاول', 'Add Contractor') }}
                    </button>
                </div>

                {{-- Add contractor form --}}
                <div x-show="addContractor" x-transition class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <form method="POST" action="{{ route('manager.development.contractors.store', $development) }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <input type="text" name="name" placeholder="{{ $tr('اسم المقاول', 'Contractor name') }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div>
                                <input type="number" name="contract_value" placeholder="{{ $tr('قيمة العقد', 'Contract value') }}" step="0.01" min="0" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div class="col-span-2">
                                <textarea name="scope_of_work" rows="2" placeholder="{{ $tr('نطاق العمل', 'Scope of work') }}" required
                                          class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm"></textarea>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-teal-600 text-white text-xs px-4 py-1.5 rounded-lg font-semibold">{{ $tr('إضافة', 'Add') }}</button>
                            <button type="button" @click="addContractor = false" class="bg-gray-200 text-gray-700 text-xs px-4 py-1.5 rounded-lg font-semibold">{{ $tr('إلغاء', 'Cancel') }}</button>
                        </div>
                    </form>
                </div>

                {{-- Contractor list --}}
                <div class="divide-y divide-gray-50 flex-1">
                    @forelse($development->contractors as $contractor)
                    <div x-data="{ payForm: false }" class="px-6 py-4">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $contractor->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($contractor->scope_of_work, 60) }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs text-gray-500">{{ number_format($contractor->contract_value) }} {{ $currency }}</p>
                                <p class="text-xs text-teal-600 font-medium">{{ $tr('مدفوع', 'Paid') }}: {{ number_format($contractor->totalPaid()) }} {{ $currency }}</p>
                            </div>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden mb-1.5">
                            @php $cpct = $contractor->paidPercent(); $cColor = $cpct >= 100 ? 'bg-green-500' : ($cpct >= 60 ? 'bg-teal-500' : 'bg-yellow-500'); @endphp
                            <div class="h-full {{ $cColor }} rounded-full" style="width: {{ $cpct }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                            <span>{{ $cpct }}% {{ $tr('مدفوع', 'paid') }}</span>
                            <span>{{ $tr('متبقي', 'remaining') }}: {{ number_format($contractor->remaining()) }} {{ $currency }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="payForm = !payForm"
                                    class="text-xs bg-blue-50 text-blue-700 hover:bg-blue-100 px-2.5 py-1 rounded-lg font-medium transition">
                                {{ $tr('تسجيل دفعة', 'Record Payment') }}
                            </button>
                            <form method="POST" action="{{ route('manager.development.contractors.destroy', [$development, $contractor]) }}"
                                  onsubmit="return confirm('{{ $tr('حذف هذا المقاول؟','Delete contractor?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition">{{ $tr('حذف', 'Remove') }}</button>
                            </form>
                        </div>
                        <div x-show="payForm" x-transition class="mt-3 bg-blue-50 rounded-lg p-3">
                            <form method="POST" action="{{ route('manager.development.contractors.payments.store', [$development, $contractor]) }}" class="space-y-2">
                                @csrf
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="number" name="amount" placeholder="{{ $tr('المبلغ', 'Amount') }}" step="0.01" min="0.01" required
                                           class="border border-gray-300 rounded-lg px-2 py-1.5 text-xs w-full">
                                    <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" required
                                           class="border border-gray-300 rounded-lg px-2 py-1.5 text-xs w-full">
                                    <div class="col-span-2">
                                        <input type="text" name="description" placeholder="{{ $tr('وصف الدفعة (اختياري)', 'Description (optional)') }}"
                                               class="border border-gray-300 rounded-lg px-2 py-1.5 text-xs w-full">
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="bg-blue-600 text-white text-xs px-3 py-1 rounded-lg font-semibold">{{ $tr('تسجيل', 'Record') }}</button>
                                    <button type="button" @click="payForm = false" class="bg-gray-200 text-gray-700 text-xs px-3 py-1 rounded-lg">{{ $tr('إلغاء', 'Cancel') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">{{ $tr('لا يوجد مقاولون مرتبطون بهذا المشروع', 'No contractors linked yet') }}</div>
                    @endforelse
                </div>
            </div>

            {{-- Expense log + Add expense --}}
            <div class="bg-white rounded-xl shadow flex flex-col" x-data="{ addExpense: false }">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">{{ $tr('سجل المصروفات', 'Expense Log') }}</h3>
                    <button @click="addExpense = !addExpense"
                            class="text-xs bg-orange-50 text-orange-700 hover:bg-orange-100 px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ $tr('إضافة مصروف', 'Add Expense') }}
                    </button>
                </div>

                {{-- Add expense form --}}
                <div x-show="addExpense" x-transition class="px-6 py-4 border-b border-gray-100 bg-orange-50/50"
                     x-data="{ qty: '', unitCost: '', total: 0 }"
                     x-init="$watch('qty', v => total = (parseFloat(v)||0) * (parseFloat(unitCost)||0));
                             $watch('unitCost', v => total = (parseFloat(qty)||0) * (parseFloat(v)||0))">
                    <form method="POST" action="{{ route('manager.development.expenses.store', $development) }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Category --}}
                            <div class="col-span-2">
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('الفئة', 'Category') }}</label>
                                <select name="category" required class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm bg-white">
                                    <option value="">{{ $tr('-- اختر الفئة --', '-- Select category --') }}</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $catLabels[$cat] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Item name --}}
                            <div class="col-span-2">
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('اسم المادة / العنصر', 'Item Name') }}</label>
                                <input type="text" name="item_name" required maxlength="255"
                                       placeholder="{{ $tr('مثال: بلاط، دهان، حديد...', 'e.g. Tiles, Paint, Steel...') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            {{-- Quantity + Unit --}}
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('الكمية', 'Quantity') }}</label>
                                <input type="number" name="quantity" x-model="qty" step="0.001" min="0.001" required
                                       placeholder="0.00"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('الوحدة (اختياري)', 'Unit (optional)') }}</label>
                                <input type="text" name="unit" maxlength="50"
                                       placeholder="{{ $tr('م², طن, يوم...', 'm², ton, day...') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            {{-- Unit cost + Live total --}}
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('تكلفة الوحدة', 'Unit Cost') }} ({{ $currency }})</label>
                                <input type="number" name="unit_cost" x-model="unitCost" step="0.01" min="0" required
                                       placeholder="0.00"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div class="flex flex-col justify-end">
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('الإجمالي', 'Total') }}</label>
                                <div class="w-full border border-orange-300 bg-orange-50 rounded-lg px-3 py-1.5 text-sm font-bold text-orange-700"
                                     x-text="total.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' {{ $currency }}'">
                                    0.00 {{ $currency }}
                                </div>
                            </div>
                            {{-- Date --}}
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('التاريخ', 'Date') }}</label>
                                <input type="date" name="expense_date" value="{{ now()->format('Y-m-d') }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            {{-- Notes --}}
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ $tr('ملاحظات (اختياري)', 'Notes (optional)') }}</label>
                                <input type="text" name="description" maxlength="500"
                                       placeholder="{{ $tr('أي تفاصيل إضافية...', 'Any extra details...') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-orange-500 text-white text-xs px-4 py-1.5 rounded-lg font-semibold">{{ $tr('إضافة', 'Add') }}</button>
                            <button type="button" @click="addExpense = false" class="bg-gray-200 text-gray-700 text-xs px-4 py-1.5 rounded-lg font-semibold">{{ $tr('إلغاء', 'Cancel') }}</button>
                        </div>
                    </form>
                </div>

                {{-- Expense list --}}
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium">{{ $tr('الفئة', 'Category') }}</th>
                                <th class="px-4 py-2 text-left font-medium">{{ $tr('الصنف', 'Item') }}</th>
                                <th class="px-4 py-2 text-right font-medium">{{ $tr('الكمية', 'Qty') }}</th>
                                <th class="px-4 py-2 text-right font-medium">{{ $tr('سعر الوحدة', 'Unit Cost') }}</th>
                                <th class="px-4 py-2 text-right font-medium">{{ $tr('الإجمالي', 'Total') }}</th>
                                <th class="px-4 py-2 text-left font-medium">{{ $tr('التاريخ', 'Date') }}</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentExpenses as $exp)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2.5">
                                    <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full font-medium whitespace-nowrap">{{ $catLabels[$exp->category] ?? $exp->category }}</span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <div class="font-medium text-gray-800 text-sm">{{ $exp->item_name }}</div>
                                    @if($exp->description)
                                    <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($exp->description, 40) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-right text-gray-700 text-sm whitespace-nowrap">
                                    {{ number_format((float)$exp->quantity, 2) }}{{ $exp->unit ? ' '.$exp->unit : '' }}
                                </td>
                                <td class="px-4 py-2.5 text-right text-gray-600 text-sm whitespace-nowrap">
                                    {{ number_format((float)$exp->unit_cost, 2) }} {{ $currency }}
                                </td>
                                <td class="px-4 py-2.5 text-right font-semibold text-orange-600 whitespace-nowrap">
                                    {{ number_format((float)$exp->amount, 2) }} {{ $currency }}
                                </td>
                                <td class="px-4 py-2.5 text-gray-400 text-xs whitespace-nowrap">{{ $exp->expense_date?->format('d M Y') }}</td>
                                <td class="px-4 py-2.5">
                                    <form method="POST" action="{{ route('manager.development.expenses.destroy', [$development, $exp]) }}"
                                          onsubmit="return confirm('{{ $tr('حذف؟','Delete?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">{{ $tr('لا توجد مصروفات بعد', 'No expenses yet') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Documents: Contracts & Invoices --}}
    <div class="bg-white rounded-xl shadow" x-data="{ uploadDoc: {{ $errors->hasAny(['type','title','file']) ? 'true' : 'false' }} }">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">{{ $tr('العقود والفواتير', 'Contracts & Invoices') }}</h3>
                    <p class="text-xs text-gray-400">{{ $development->documents->count() }} {{ $tr('مستند', 'document(s)') }}</p>
                </div>
            </div>
            <button @click="uploadDoc = !uploadDoc"
                    class="text-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                {{ $tr('رفع مستند', 'Upload Document') }}
            </button>
        </div>

        {{-- Upload form --}}
        <div x-show="uploadDoc" x-transition class="px-6 py-4 border-b border-gray-100 bg-indigo-50/40">
            <form id="doc-upload-form" method="POST" action="{{ route('manager.development.documents.store', $development) }}"
                  enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $tr('نوع المستند', 'Document Type') }}</label>
                        <select name="type" required class="w-full border rounded-lg px-3 py-1.5 text-sm bg-white {{ $errors->has('type') ? 'border-red-400' : 'border-gray-300' }}">
                            @foreach(\App\Models\DevelopmentDocument::typeLabels($isAr) as $val => $label)
                            <option value="{{ $val }}" @selected(old('type') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $tr('العنوان', 'Title') }}</label>
                        <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                               placeholder="{{ $tr('مثال: عقد المقاول الرئيسي', 'e.g. Main Contractor Agreement') }}"
                               class="w-full border rounded-lg px-3 py-1.5 text-sm {{ $errors->has('title') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $tr('الملف', 'File') }} <span class="text-gray-400">(PDF, JPG, PNG, DOC — max 10 MB)</span></label>
                        <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                               class="w-full border rounded-lg px-3 py-1.5 text-sm bg-white file:mr-2 file:text-xs file:border-0 file:bg-indigo-100 file:text-indigo-700 file:rounded file:px-2 file:py-0.5 {{ $errors->has('file') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-indigo-600 text-white text-xs px-4 py-1.5 rounded-lg font-semibold">{{ $tr('رفع', 'Upload') }}</button>
                    <button type="button" @click="uploadDoc = false" class="bg-gray-200 text-gray-700 text-xs px-4 py-1.5 rounded-lg font-semibold">{{ $tr('إلغاء', 'Cancel') }}</button>
                </div>
            </form>
        </div>

        {{-- Document list --}}
        @forelse($development->documents->sortByDesc('created_at') as $doc)
        @php
            $docTypeColors = ['contract' => 'bg-blue-50 text-blue-700', 'invoice' => 'bg-amber-50 text-amber-700', 'other' => 'bg-gray-100 text-gray-600'];
            $ext = $doc->extension();
            $isPdf = $doc->isPdf();
        @endphp
        <div class="flex items-center gap-4 px-6 py-3 border-b border-gray-50 hover:bg-gray-50 transition">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                        {{ $isPdf ? 'bg-red-50' : (in_array($ext,['jpg','jpeg','png']) ? 'bg-green-50' : 'bg-blue-50') }}">
                @if($isPdf)
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                @elseif(in_array($ext, ['jpg','jpeg','png']))
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 20M14 8h.01"/></svg>
                @else
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $docTypeColors[$doc->type] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $doc->typeLabel($isAr) }}
                    </span>
                    <span class="font-medium text-gray-800 text-sm truncate">{{ $doc->title }}</span>
                </div>
                <div class="text-xs text-gray-400 mt-0.5">{{ $doc->original_name }} · {{ $doc->created_at->format('d M Y') }}</div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ $doc->url() }}" target="_blank"
                   class="text-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-2.5 py-1 rounded-lg font-medium transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    {{ $tr('تنزيل', 'Download') }}
                </a>
                <form method="POST" action="{{ route('manager.development.documents.destroy', [$development, $doc]) }}"
                      onsubmit="return confirm('{{ $tr('حذف هذا المستند؟','Delete this document?') }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="px-6 py-10 text-center text-gray-400 text-sm">
            <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            {{ $tr('لا توجد مستندات مرفوعة بعد', 'No documents uploaded yet') }}
        </div>
        @endforelse
    </div>

    </div>{{-- /py-4 space-y-6 --}}

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // PHP 8.5 multipart/form-data CSRF workaround: send token via header instead of body
        (function () {
            var form = document.getElementById('doc-upload-form');
            if (!form) return;
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var btn = form.querySelector('[type="submit"]');
                var origText = btn ? btn.textContent : null;
                if (btn) { btn.disabled = true; btn.textContent = '...'; }
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-CSRF-TOKEN': token },
                    redirect: 'follow',
                    credentials: 'same-origin'
                }).then(function (r) {
                    window.location.href = r.url || window.location.href;
                }).catch(function () {
                    if (btn) { btn.disabled = false; btn.textContent = origText; }
                });
            });
        })();
    </script>
    <script>
        new Chart(document.getElementById('devMonthlyChart'), {
            data: {
                labels: @json($monthlyChart->pluck('label')),
                datasets: [
                    {
                        type: 'bar',
                        label: '{{ $tr('الصرف الفعلي', 'Actual Spend') }}',
                        data: @json($monthlyChart->pluck('actual')),
                        backgroundColor: 'rgba(13,148,136,0.7)',
                        borderRadius: 6,
                    },
                    {
                        type: 'line',
                        label: '{{ $tr('الميزانية الشهرية', 'Expected Monthly') }}',
                        data: @json($monthlyChart->pluck('expected')),
                        borderColor: '#94a3b8',
                        borderDash: [5, 5],
                        borderWidth: 2,
                        fill: false,
                        pointRadius: 0,
                        tension: 0,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() } } }
            }
        });
    </script>
    @endpush
</x-app-layout>
