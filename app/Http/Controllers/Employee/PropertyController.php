<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeCommission;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    private const PROPERTY_SALE_COMMISSION_RATE = 3.00;

    public function markSold(Request $request, Property $property)
    {
        $employee = $request->user();
        abort_if((int) $property->employee_id !== (int) $employee->id, 403);
        abort_if(! $this->propertySupportsSale($property), 422, app()->getLocale() === 'en'
            ? 'This property is configured for rent only and cannot be marked as sold.'
            : 'هذا العقار مخصص للإيجار فقط ولا يمكن تسجيله كمباع');

        $validated = $request->validate([
            'sale_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $computedSaleAmount = $property->units()
            ->whereIn('listing_type', ['sale', 'both'])
            ->whereNotNull('sale_price')
            ->sum('sale_price');
        $saleAmount = (float) ($validated['sale_amount'] ?? $computedSaleAmount);
        abort_if($saleAmount <= 0, 422, app()->getLocale() === 'en'
            ? 'A valid sale amount is required to calculate commission.'
            : 'يجب إدخال قيمة بيع صحيحة لاحتساب العمولة');

        DB::transaction(function () use ($property, $employee, $saleAmount, $validated) {
            $property->update(['status' => 'sold']);
            $property->units()->where('status', '!=', 'sold')->update(['status' => 'sold']);

            $rate = self::PROPERTY_SALE_COMMISSION_RATE;
            $commissionAmount = round($saleAmount * ($rate / 100), 2);

            EmployeeCommission::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'property_id' => $property->id,
                    'type' => 'property_sale',
                ],
                [
                    'base_amount' => $saleAmount,
                    'rate' => $rate,
                    'commission_amount' => $commissionAmount,
                    'notes' => $validated['notes'] ?? null,
                    'recorded_at' => now(),
                ]
            );
        });

        return redirect()->route('employee.dashboard')->with(
            'success',
            app()->getLocale() === 'en'
                ? 'Property marked as sold and commission was calculated automatically.'
                : 'تم تسجيل العقار كمباع واحتساب عمولة البيع تلقائياً'
        );
    }

    private function propertySupportsSale(Property $property): bool
    {
        if (in_array($property->purpose, ['sale', 'both'], true)) {
            return true;
        }

        return $property->units()
            ->whereIn('listing_type', ['sale', 'both'])
            ->exists();
    }
}
