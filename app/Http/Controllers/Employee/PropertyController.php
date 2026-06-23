<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeCommission;
use App\Models\Owner;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    private const PROPERTY_SALE_COMMISSION_RATE = 3.00;

    public function index(Request $request)
    {
        $employee = $request->user();

        $properties = Property::where('employee_id', $employee->id)
            ->orWhere('referral_employee_id', $employee->id)
            ->with(['units', 'owner.user'])
            ->latest()
            ->paginate(20);

        return view('employee.properties.index', compact('properties'));
    }

    public function create()
    {
        $owners = Owner::with('user')->get();
        return view('employee.properties.create', compact('owners'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code'                       => 'nullable|string|max:50|unique:properties,code',
            'name_ar'                    => 'required|string|max:255',
            'name_en'                    => 'nullable|string|max:255',
            'type'                       => 'required|in:apartment_building,villa,farm,chalet,flat,land,office,shop',
            'purpose'                    => 'required|in:rent,sale,both',
            'section'                    => 'required|in:hoa,management,external',
            'address_ar'                 => 'required|string|max:500',
            'address_en'                 => 'nullable|string|max:500',
            'city_ar'                    => 'nullable|string|max:100',
            'city_en'                    => 'nullable|string|max:100',
            'description_ar'             => 'nullable|string',
            'description_en'             => 'nullable|string',
            'owner_id'                   => 'nullable|exists:owners,id',
            'floors'                     => 'nullable|integer|min:1',
            'total_area'                 => 'nullable|numeric|min:0',
            'bedrooms'                   => 'nullable|integer|min:0',
            'bathrooms'                  => 'nullable|integer|min:0',
            'electricity_account_number' => 'nullable|string|max:100',
            'water_account_number'       => 'nullable|string|max:100',
        ]);

        $validated['employee_id']              = $request->user()->id;
        $validated['referral_employee_id']     = $request->user()->id;
        $validated['referral_commission_rate'] = $request->user()->commission_rate ?? null;
        $validated['created_by']               = $request->user()->id;
        $validated['code']                     = $validated['code'] ?? $this->generateCode($validated['type']);
        $validated['name']        = $validated['name_ar'];
        $validated['address']     = $validated['address_ar'];
        $validated['city']        = $validated['city_ar'] ?? null;
        $validated['description'] = $validated['description_ar'] ?? null;

        $property = Property::create($validated);

        if (in_array($property->type, ['villa', 'farm', 'chalet', 'flat', 'land'])) {
            $property->units()->create([
                'type'         => $property->type . '_unit',
                'area'         => $property->total_area,
                'bedrooms'     => $property->bedrooms,
                'bathrooms'    => $property->bathrooms,
                'listing_type' => $property->purpose === 'both' ? 'both' : $property->purpose,
                'rent_price'   => $request->input('rent_price'),
                'sale_price'   => $request->input('sale_price'),
                'status'       => 'available',
            ]);
        }

        $msg = app()->getLocale() === 'ar' ? 'تم إضافة العقار بنجاح.' : 'Property added successfully.';
        return redirect()->route('employee.properties.index')->with('success', $msg);
    }

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

    public function destroy(Property $property): RedirectResponse
    {
        abort_if($property->created_by !== auth()->id(), 403);
        $property->delete();
        $msg = app()->getLocale() === 'ar' ? 'تم حذف العقار بنجاح' : 'Property deleted successfully';
        return redirect()->route('employee.properties.index')->with('success', $msg);
    }

    private function generateCode(string $type): string
    {
        $prefix = match ($type) {
            'apartment_building' => 'TH-B',
            'villa'              => 'TH-V',
            'farm'               => 'TH-F',
            'chalet'             => 'TH-C',
            'flat'               => 'TH-FL',
            'land'               => 'TH-L',
            'office'             => 'TH-OF',
            'shop'               => 'TH-SH',
            default              => 'TH',
        };
        do {
            $last = Property::where('code', 'like', $prefix . '-%')
                ->orderByRaw('CAST(SUBSTRING_INDEX(code, \'-\', -1) AS UNSIGNED) DESC')
                ->value('code');
            $next = $last ? ((int) preg_replace('/.*-(\d+)$/', '$1', $last)) + 1 : 1;
            $code = sprintf('%s-%03d', $prefix, $next);
        } while (Property::where('code', $code)->exists());
        return $code;
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
