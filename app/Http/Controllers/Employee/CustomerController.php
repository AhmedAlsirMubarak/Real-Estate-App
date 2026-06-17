<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $status  = $request->input('status');
        $purpose = $request->input('purpose');
        $type    = $request->input('type');

        $customers = Customer::query()
            ->when($search, fn($q) => $q->where(fn($q) => $q
                ->where('name',     'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%")
                ->orWhere('email',  'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")
            ))
            ->when($status,  fn($q) => $q->where('status', $status))
            ->when($purpose, fn($q) => $q->where('purpose', $purpose))
            ->when($type && $type !== 'any', fn($q) => $q->where('property_type', $type))
            ->latest()
            ->paginate(20)
            ->appends($request->query());

        return view('employee.customers.index', compact('customers', 'search', 'status', 'purpose', 'type'));
    }

    public function create()
    {
        return view('employee.customers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Customer::create($data);

        return redirect()->route('employee.customers.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تمت إضافة العميل بنجاح' : 'Customer added successfully');
    }

    public function show(Customer $customer)
    {
        return view('employee.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('employee.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $this->validated($request);
        $customer->update($data);

        return redirect()->route('employee.customers.show', $customer)
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث العميل بنجاح' : 'Customer updated successfully');
    }

    public function reply(Request $request, Customer $customer)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,interested,closed',
            'reply_note' => 'nullable|string|max:2000',
        ]);

        $customer->status = $request->status;

        if ($request->filled('reply_note')) {
            $timestamp = now()->format('Y/m/d H:i');
            $prefix = app()->getLocale() === 'ar' ? "[$timestamp] رد: " : "[$timestamp] Reply: ";
            $customer->notes = trim(($customer->notes ? $customer->notes . "\n\n" : '') . $prefix . $request->reply_note);
        }

        $customer->save();

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم الحفظ بنجاح' : 'Saved successfully');
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
