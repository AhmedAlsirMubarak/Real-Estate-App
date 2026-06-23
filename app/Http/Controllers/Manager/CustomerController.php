<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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


    public function importForm()
    {
        return view('manager.customers.import');
    }

    public function downloadTemplate()
    {
        $q = fn($v) => '"' . str_replace('"', '""', (string)($v ?? '')) . '"';
        $rows = [
            ['name', 'mobile', 'email', 'location', 'property_type', 'purpose', 'min_budget', 'max_budget', 'bedrooms', 'status', 'source', 'notes'],
            ['Ahmed Ali', '0512345678', 'ahmed@example.com', 'بوشر', 'villa', 'rent', '500', '1000', '3', 'new', 'instagram', ''],
        ];
        $csv = "\xEF\xBB\xBF";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map($q, $row)) . "\r\n";
        }
        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="customers-import-template.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:10240']]);

        $handle = fopen($request->file('file')->getPathname(), 'r');
        $rawHeaders = fgetcsv($handle);
        if (!$rawHeaders) {
            fclose($handle);
            return back()->with('import_results', ['imported' => 0, 'errors' => [], 'warnings' => ['File appears empty or unreadable.']]);
        }

        $headers = array_map(fn($h) => strtolower(trim(str_replace(["\xEF\xBB\xBF", '"', "'"], '', $h))), $rawHeaders);

        $validTypes    = array_keys(Customer::$propertyTypes);
        $validPurposes = array_keys(Customer::$purposes);
        $validStatuses = array_keys(Customer::$statuses);
        $validSources  = array_keys(Customer::$sources);
        $validLocs     = array_keys(Customer::$locations);

        $imported = 0;
        $errors   = [];
        $rowNum   = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            $data = [];
            foreach ($headers as $i => $col) {
                $data[$col] = isset($row[$i]) ? trim($row[$i]) : '';
            }
            if (!array_filter($data)) continue;

            if (empty($data['name'] ?? '')) {
                $errors[] = ['row' => $rowNum, 'field' => 'name', 'value' => '', 'error' => 'Required'];
                continue;
            }

            $type = $data['property_type'] ?? '';
            if ($type !== '' && !in_array($type, $validTypes)) {
                $errors[] = ['row' => $rowNum, 'field' => 'property_type', 'value' => $type, 'error' => 'Valid: ' . implode(', ', $validTypes)];
                continue;
            }

            $purpose = $data['purpose'] ?? '';
            if ($purpose !== '' && !in_array($purpose, $validPurposes)) {
                $errors[] = ['row' => $rowNum, 'field' => 'purpose', 'value' => $purpose, 'error' => 'Valid: ' . implode(', ', $validPurposes)];
                continue;
            }

            Customer::create([
                'created_by'    => Auth::id(),
                'name'          => $data['name'],
                'mobile'        => $data['mobile'] ?: null,
                'email'         => $data['email'] ?: null,
                'location'      => ($data['location'] ?? '') && in_array($data['location'], $validLocs) ? $data['location'] : null,
                'property_type' => in_array($type, $validTypes) ? $type : 'any',
                'purpose'       => in_array($purpose, $validPurposes) ? $purpose : 'both',
                'min_budget'    => is_numeric($data['min_budget'] ?? '') ? (int)$data['min_budget'] : null,
                'max_budget'    => is_numeric($data['max_budget'] ?? '') ? (int)$data['max_budget'] : null,
                'bedrooms'      => is_numeric($data['bedrooms'] ?? '') ? (int)$data['bedrooms'] : null,
                'status'        => in_array($data['status'] ?? '', $validStatuses) ? $data['status'] : 'new',
                'source'        => in_array($data['source'] ?? '', $validSources) ? $data['source'] : null,
                'notes'         => $data['notes'] ?: null,
            ]);
            $imported++;
        }

        fclose($handle);
        return back()->with('import_results', compact('imported', 'errors'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'          => 'required|string|max:255',
            'mobile'        => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:255',
            'location'      => 'nullable|string|max:255',
            'property_type' => 'required|in:any,apartment_building,villa,farm,chalet,office,shop',
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
