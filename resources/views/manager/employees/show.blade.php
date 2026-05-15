<x-app-layout>
    <x-slot name="title">{{ $employee->name }}</x-slot>
    <div class="py-4">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('manager.employees.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $employee->name }}</h2>
            <a href="{{ route('manager.employees.edit', $employee) }}" class="mr-auto bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">تعديل</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">بيانات الموظف</h3>
                <dl class="space-y-3">
                    <div><dt class="text-xs text-gray-500 mb-1">الاسم</dt><dd class="font-medium">{{ $employee->name }}</dd></div>
                    <div><dt class="text-xs text-gray-500 mb-1">البريد الإلكتروني</dt><dd class="font-medium text-sm">{{ $employee->email }}</dd></div>
                    <div><dt class="text-xs text-gray-500 mb-1">الهاتف</dt><dd class="font-medium">{{ $employee->phone ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-500 mb-1">الدور</dt>
                        <dd><span class="px-2 py-1 rounded-full text-xs font-medium {{ $employee->hasRole('accountant') ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $employee->hasRole('accountant') ? 'محاسب' : 'موظف' }}
                        </span></dd>
                    </div>
                    <div><dt class="text-xs text-gray-500 mb-1">تاريخ التسجيل</dt><dd class="font-medium">{{ $employee->created_at->format('Y/m/d') }}</dd></div>
                </dl>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">العقارات المسندة ({{ $employee->managedProperties->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-right font-medium">اسم العقار</th>
                                <th class="px-4 py-3 text-right font-medium">النوع</th>
                                <th class="px-4 py-3 text-right font-medium">الوحدات</th>
                                <th class="px-4 py-3 text-right font-medium">إجراء</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($employee->managedProperties as $property)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $property->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $property->typeLabel() }}</td>
                                <td class="px-4 py-3"><span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs">{{ $property->units->count() }}</span></td>
                                <td class="px-4 py-3"><a href="{{ route('manager.properties.show', $property) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">عرض</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">لم يُسند له أي عقار</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>