<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;


class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $status   = $request->input('status');
        $purpose  = $request->input('purpose');
        $type     = $request->input('type');

        $customers = Customer::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name',     'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%")
                      ->orWhere('email',  'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($status,  fn($q) => $q->where('status', $status))
            ->when($purpose, fn($q) => $q->where('purpose', $purpose))
            ->when($type && $type !== 'any', fn($q) => $q->where('property_type', $type))
            ->latest()
            ->paginate(20)
            ->appends($request->query());

        return view('manager.customers.index', compact('customers', 'search', 'status', 'purpose', 'type'));
    }

    public function create()
    {
        return view('manager.customers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Customer::create($data);

        return redirect()->route('manager.customers.index')
            ->with('success', __('Customer added successfully'));
    }

    public function show(Customer $customer)
    {
        return view('manager.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('manager.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $this->validated($request);
        $customer->update($data);

        return redirect()->route('manager.customers.index')
            ->with('success', __('Customer updated successfully'));
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', __('Customer deleted'));
    }

    public function bulkDestroy(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')));
        if (empty($ids)) {
            return back()->with('error', 'لم يتم تحديد أي عميل');
        }

        $count = Customer::whereIn('id', $ids)->count();
        Customer::whereIn('id', $ids)->delete();

        return back()->with('success', 'تم حذف ' . $count . ' عميل');
    }

    public function export(Request $request)
    {
        $isAr    = app()->getLocale() === 'ar';
        $locale  = $isAr ? 'ar' : 'en';
        $search  = $request->input('search');
        $status  = $request->input('status');
        $purpose = $request->input('purpose');
        $type    = $request->input('type');

        $customers = Customer::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name',     'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%")
                      ->orWhere('email',  'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($status,  fn($q) => $q->where('status', $status))
            ->when($purpose, fn($q) => $q->where('purpose', $purpose))
            ->when($type && $type !== 'any', fn($q) => $q->where('property_type', $type))
            ->latest()
            ->get();

        $headers = $isAr
            ? ['الاسم', 'الجوال', 'البريد الإلكتروني', 'المنطقة', 'نوع العقار', 'الغرض', 'الحد الأدنى للميزانية', 'الحد الأقصى للميزانية', 'غرف النوم', 'الحالة', 'المصدر', 'ملاحظات', 'تاريخ الإضافة']
            : ['Name', 'Mobile', 'Email', 'Location', 'Property Type', 'Purpose', 'Min Budget', 'Max Budget', 'Bedrooms', 'Status', 'Source', 'Notes', 'Created At'];

        $rows = [$headers];
        foreach ($customers as $c) {
            $rows[] = [
                $c->name, $c->mobile, $c->email, $c->location,
                $c->typeLabel($locale), $c->purposeLabel($locale),
                $c->min_budget, $c->max_budget, $c->bedrooms,
                $c->statusLabel($locale), $c->source, $c->notes,
                $c->created_at?->format('Y-m-d'),
            ];
        }

        $csv = "\xEF\xBB\xBF";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', (string)($v ?? '')) . '"', $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="customers-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }


    private function validated(Request $request): array
    {
        return $request->validate([
            'name'          => 'required|string|max:255',
            'mobile'        => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:255',
            'location'      => 'nullable|string|max:255',
            'property_type' => 'required|in:any,apartment_building,villa,farm,chalet',
            'purpose'       => 'required|in:rent,sale,both',
            'min_budget'    => 'nullable|numeric|min:0',
            'max_budget'    => 'nullable|numeric|min:0',
            'bedrooms'      => 'nullable|integer|min:0|max:20',
            'notes'         => 'nullable|string',
            'status'        => 'required|in:new,contacted,interested,closed,done',
            'source'        => 'nullable|in:open_market,instagram,facebook,tiktok,dubizzle,website,billboard,referral,other',
        ]);
    }
}
