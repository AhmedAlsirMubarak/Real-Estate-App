<x-app-layout>
    @php
        $isAr     = app()->getLocale() === 'ar';
        $tr       = fn (string $ar, string $en) => $isAr ? $ar : $en;
        $currency = $isAr ? 'ر.ع' : 'OMR';
    @endphp
    <x-slot name="title">{{ $tr('مشروع تطوير جديد', 'New Development Project') }}</x-slot>

    <div class="py-4 max-w-4xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('manager.development.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-900">{{ $tr('مشروع تطوير جديد', 'New Development Project') }}</h2>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4 mb-4">
            <p class="font-semibold text-sm mb-1">{{ $tr('يوجد أخطاء في النموذج، يرجى التحقق:', 'Please fix the following errors:') }}</p>
            <ul class="list-disc list-inside text-sm space-y-0.5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('manager.development.store') }}" class="space-y-6">
            @csrf

            {{-- Basic info --}}
            <div class="bg-white rounded-xl shadow p-6 space-y-4">
                <h3 class="font-bold text-gray-800 pb-2 border-b border-gray-100">{{ $tr('معلومات المشروع', 'Project Information') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('اسم المشروع', 'Project Name') }} *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('النوع', 'Type') }} *</label>
                        <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                            <option value="">{{ $tr('-- اختر --', '-- Select --') }}</option>
                            <option value="residential" @selected(old('type') === 'residential')>{{ $tr('سكني', 'Residential') }}</option>
                            <option value="commercial"  @selected(old('type') === 'commercial')>{{ $tr('تجاري', 'Commercial') }}</option>
                            <option value="mixed"       @selected(old('type') === 'mixed')>{{ $tr('متعدد الاستخدام', 'Mixed Use') }}</option>
                        </select>
                        @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('الموقع', 'Location') }} *</label>
                        <input type="text" name="location" value="{{ old('location') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                        @error('location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('مدير المشروع', 'Project Manager') }} *</label>
                        <input type="text" name="project_manager_name" value="{{ old('project_manager_name') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                        @error('project_manager_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Timeline & Status --}}
            <div class="bg-white rounded-xl shadow p-6 space-y-4">
                <h3 class="font-bold text-gray-800 pb-2 border-b border-gray-100">{{ $tr('الجدول الزمني والحالة', 'Timeline & Status') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ البداية', 'Start Date') }} *</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                        @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('تاريخ الانتهاء المتوقع', 'Estimated Completion') }} *</label>
                        <input type="date" name="estimated_completion_date" value="{{ old('estimated_completion_date') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                        @error('estimated_completion_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('المرحلة الحالية', 'Current Phase') }} *</label>
                        <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                            @foreach(['planning' => $tr('تخطيط','Planning'), 'foundation' => $tr('أساسات','Foundation'), 'structure' => $tr('هيكل','Structure'), 'finishing' => $tr('تشطيب','Finishing'), 'handover' => $tr('تسليم','Handover'), 'completed' => $tr('مكتمل','Completed')] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status', 'planning') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('نسبة الإنجاز %', 'Progress %') }} *</label>
                        <input type="number" name="progress_percentage" value="{{ old('progress_percentage', 0) }}"
                               min="0" max="100" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                        @error('progress_percentage')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Budget --}}
            @php
                $catsData      = [];
                $allocatedExpr = collect($categories)->map(fn($c) => "this.catTotal('" . addslashes($c) . "')")->join('+');
                foreach ($categories as $cat) {
                    $oldJson = old('category_budgets_json.' . $cat);
                    $catsData[$cat] = $oldJson
                        ? (json_decode($oldJson, true) ?: [['name'=>'','amount'=>'']])
                        : [['name'=>'','amount'=>'']];
                }
            @endphp
            <script>
                function createBudgetComponent() {
                    return {
                        totalBudget: {!! json_encode((float) old('total_budget', 0)) !!},
                        open: {!! json_encode(array_fill_keys($categories, false)) !!},
                        cats: {!! json_encode($catsData) !!},
                        catTotal(k) { return this.cats[k].reduce((s,i) => s + (parseFloat(i.amount)||0), 0); },
                        get allocated() { return {!! $allocatedExpr !!}; },
                        get remaining() { return (parseFloat(this.totalBudget)||0) - this.allocated; },
                        get pct() { let t=parseFloat(this.totalBudget)||0; return t>0 ? Math.min(100,Math.round((this.allocated/t)*100)) : 0; },
                        get barColor() { return this.remaining<0 ? 'bg-red-500' : (this.pct>=90 ? 'bg-orange-400' : 'bg-teal-500'); },
                        get textColor() { return this.remaining<0 ? 'text-red-600' : (this.pct>=90 ? 'text-orange-600' : 'text-teal-600'); },
                        addItem(k) { this.cats[k].push({name:'',amount:''}); this.open[k]=true; },
                        removeItem(k,i) { if(this.cats[k].length>1) this.cats[k].splice(i,1); },
                        fmt(n) { return Number(n||0).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
                    };
                }
            </script>
            <div class="bg-white rounded-xl shadow p-6 space-y-4" x-data="createBudgetComponent()">
                <h3 class="font-bold text-gray-800 pb-2 border-b border-gray-100">{{ $tr('الميزانية', 'Budget') }}</h3>

                {{-- Total budget --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('إجمالي الميزانية المعتمدة', 'Total Approved Budget') }} ({{ $currency }}) *</label>
                    <input type="number" name="total_budget" x-model="totalBudget"
                           value="{{ old('total_budget') }}" step="0.01" min="0" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                    @error('total_budget')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Allocation header --}}
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-500 font-medium">{{ $tr('توزيع الميزانية على الفئات (اختياري)', 'Budget allocation per category (optional)') }}</p>
                    <span class="text-xs font-semibold" :class="textColor" x-show="allocated > 0" x-text="pct + '% {{ $tr('مُوزَّع','allocated') }}'"></span>
                </div>

                {{-- Category cards --}}
                <div class="space-y-2">
                    @foreach($categories as $cat)
                    <input type="hidden" name="category_budgets_json[{{ $cat }}]" :value="JSON.stringify(cats['{{ $cat }}'])">

                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        {{-- Card header --}}
                        <button type="button" @click="open['{{ $cat }}'] = !open['{{ $cat }}']"
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-gray-50 hover:bg-gray-100 transition text-left">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="open['{{ $cat }}'] ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">{{ $catLabels[$cat] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400" x-show="cats['{{ $cat }}'].filter(i=>i.amount).length > 0"
                                      x-text="cats['{{ $cat }}'].filter(i=>i.amount).length + ' {{ $tr('بند','item') }}'"></span>
                                <span class="text-sm font-semibold"
                                      :class="catTotal('{{ $cat }}') > 0 ? 'text-teal-600' : 'text-gray-300'"
                                      x-text="catTotal('{{ $cat }}') > 0 ? fmt(catTotal('{{ $cat }}')) + ' {{ $currency }}' : '— {{ $currency }}'"></span>
                            </div>
                        </button>

                        {{-- Expandable item rows --}}
                        <div x-show="open['{{ $cat }}']" x-transition class="divide-y divide-gray-50 bg-white">
                            <template x-for="(item, idx) in cats['{{ $cat }}']" :key="idx">
                                <div class="flex items-center gap-2 px-4 py-2">
                                    <input type="text" x-model="item.name"
                                           placeholder="{{ $tr('اسم البند (مثال: بلاط، حديد...)', 'Item name (e.g. Tiles, Steel...)') }}"
                                           class="flex-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-sm focus:ring-1 focus:ring-teal-400 focus:border-teal-400">
                                    <input type="number" x-model="item.amount"
                                           placeholder="0.00" step="0.01" min="0"
                                           class="w-32 border border-gray-200 rounded-lg px-2.5 py-1.5 text-sm text-right focus:ring-1 focus:ring-teal-400 focus:border-teal-400">
                                    <span class="text-xs text-gray-400 shrink-0">{{ $currency }}</span>
                                    <button type="button" @click="removeItem('{{ $cat }}', idx)"
                                            x-show="cats['{{ $cat }}'].length > 1"
                                            class="text-red-300 hover:text-red-500 transition shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            {{-- Footer: add + subtotal --}}
                            <div class="flex items-center justify-between px-4 py-2 bg-gray-50/60">
                                <button type="button" @click="addItem('{{ $cat }}')"
                                        class="text-xs text-teal-600 hover:text-teal-800 font-medium flex items-center gap-1 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ $tr('إضافة بند', 'Add item') }}
                                </button>
                                <span class="text-xs text-gray-500">
                                    {{ $tr('المجموع','Subtotal') }}:
                                    <span class="font-semibold text-teal-700" x-text="fmt(catTotal('{{ $cat }}')) + ' {{ $currency }}'"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Live allocation bar --}}
                <div x-show="allocated > 0 || parseFloat(totalBudget) > 0" class="pt-1 space-y-2">
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300" :class="barColor" :style="'width:' + pct + '%'"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">{{ $tr('إجمالي الموزَّع','Total allocated') }}: <span class="font-semibold" :class="textColor" x-text="fmt(allocated) + ' {{ $currency }}'"></span></span>
                        <span :class="remaining < 0 ? 'text-red-500 font-semibold' : 'text-gray-400'">
                            <span x-text="remaining < 0 ? '{{ $tr('تجاوز:', 'Over by:') }}' : '{{ $tr('غير مُوزَّع:', 'Unallocated:') }}'"></span>
                            <span x-text="fmt(Math.abs(remaining)) + ' {{ $currency }}'"></span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="bg-white rounded-xl shadow p-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $tr('ملاحظات', 'Notes') }}</label>
                <textarea name="notes" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                    {{ $tr('إنشاء المشروع', 'Create Project') }}
                </button>
                <a href="{{ route('manager.development.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                    {{ $tr('إلغاء', 'Cancel') }}
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
