<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
        $data['created_by'] = auth()->id();
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

    public function export(Request $request)
    {
        $isAr    = app()->getLocale() === 'ar';
        $search  = $request->input('search');
        $status  = $request->input('status');
        $purpose = $request->input('purpose');

        $customers = Customer::query()
            ->when($search, fn($q) => $q->where(fn($q) => $q
                ->where('name',     'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%")
                ->orWhere('email',  'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")
            ))
            ->when($status,  fn($q) => $q->where('status', $status))
            ->when($purpose, fn($q) => $q->where('purpose', $purpose))
            ->latest()
            ->get();

        $locale = $isAr ? 'ar' : 'en';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = $isAr
            ? ['الاسم', 'الجوال', 'البريد الإلكتروني', 'المنطقة', 'نوع العقار', 'الغرض', 'الحد الأدنى للميزانية', 'الحد الأقصى للميزانية', 'غرف النوم', 'الحالة', 'المصدر', 'ملاحظات', 'تاريخ الإضافة']
            : ['Name', 'Mobile', 'Email', 'Location', 'Property Type', 'Purpose', 'Min Budget', 'Max Budget', 'Bedrooms', 'Status', 'Source', 'Notes', 'Created At'];

        $sheet->fromArray([$headers], null, 'A1');

        $row = 2;
        foreach ($customers as $c) {
            $sheet->fromArray([[
                $c->name,
                $c->mobile,
                $c->email,
                $c->location,
                $c->typeLabel($locale),
                $c->purposeLabel($locale),
                $c->min_budget,
                $c->max_budget,
                $c->bedrooms,
                $c->statusLabel($locale),
                $c->source,
                $c->notes,
                $c->created_at?->format('Y-m-d'),
            ]], null, "A{$row}");
            $row++;
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'customers-' . now()->format('Y-m-d') . '.xlsx', [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="customers-' . now()->format('Y-m-d') . '.xlsx"',
        ]);
    }

    public function destroy(Customer $customer)
    {
        abort_if($customer->created_by !== auth()->id(), 403);
        $customer->delete();
        $msg = app()->getLocale() === 'ar' ? 'تم حذف العميل بنجاح' : 'Customer deleted successfully';
        return redirect()->route('employee.customers.index')->with('success', $msg);
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
