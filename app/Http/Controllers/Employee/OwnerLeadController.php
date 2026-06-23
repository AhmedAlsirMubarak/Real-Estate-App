<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\OwnerLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerLeadController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $status  = $request->input('status');
        $purpose = $request->input('purpose');

        $ownerLeads = OwnerLead::when($search, fn($q) => $q->where(fn($sq) =>
                $sq->where('name', 'like', "%$search%")
                   ->orWhere('mobile', 'like', "%$search%")
                   ->orWhere('email', 'like', "%$search%")
                   ->orWhere('location', 'like', "%$search%")
            ))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($purpose, fn($q) => $q->where('purpose', $purpose))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('employee.owner-leads.index', compact('ownerLeads', 'search', 'status', 'purpose'));
    }

    public function create()
    {
        return view('employee.owner-leads.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'mobile'        => ['nullable', 'string', 'max:50'],
            'email'         => ['nullable', 'email', 'max:255'],
            'location'      => ['nullable', 'string', 'max:255'],
            'property_type' => ['nullable', 'string', 'max:100'],
            'purpose'       => ['nullable', 'string', 'max:50'],
            'min_budget'    => ['nullable', 'integer', 'min:0'],
            'max_budget'    => ['nullable', 'integer', 'min:0'],
            'bedrooms'      => ['nullable', 'integer', 'min:0', 'max:20'],
            'notes'         => ['nullable', 'string', 'max:5000'],
            'status'        => ['nullable', 'string', 'max:50'],
            'source'        => ['nullable', 'string', 'max:100'],
        ]);

        $data['created_by'] = Auth::id();
        OwnerLead::create($data);

        $isAr = app()->getLocale() === 'ar';
        return redirect()->route('employee.owner-leads.index')
            ->with('success', $isAr ? 'تم إضافة المالك بنجاح' : 'Owner added successfully');
    }

    public function show(OwnerLead $ownerLead)
    {
        return view('employee.owner-leads.show', compact('ownerLead'));
    }

    public function edit(OwnerLead $ownerLead)
    {
        return view('employee.owner-leads.edit', compact('ownerLead'));
    }

    public function update(Request $request, OwnerLead $ownerLead)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'mobile'        => ['nullable', 'string', 'max:50'],
            'email'         => ['nullable', 'email', 'max:255'],
            'location'      => ['nullable', 'string', 'max:255'],
            'property_type' => ['nullable', 'string', 'max:100'],
            'purpose'       => ['nullable', 'string', 'max:50'],
            'min_budget'    => ['nullable', 'integer', 'min:0'],
            'max_budget'    => ['nullable', 'integer', 'min:0'],
            'bedrooms'      => ['nullable', 'integer', 'min:0', 'max:20'],
            'notes'         => ['nullable', 'string', 'max:5000'],
            'status'        => ['nullable', 'string', 'max:50'],
            'source'        => ['nullable', 'string', 'max:100'],
        ]);

        $ownerLead->update($data);

        $isAr = app()->getLocale() === 'ar';
        return redirect()->route('employee.owner-leads.index')
            ->with('success', $isAr ? 'تم تحديث بيانات المالك' : 'Owner updated successfully');
    }

    public function destroy(OwnerLead $ownerLead)
    {
        abort_if($ownerLead->created_by !== Auth::id(), 403);
        $ownerLead->delete();
        $isAr = app()->getLocale() === 'ar';
        return back()->with('success', $isAr ? 'تم حذف المالك' : 'Owner deleted');
    }

    public function importForm()
    {
        return view('employee.owner-leads.import');
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
            'Content-Disposition' => 'attachment; filename="owner-leads-import-template.csv"',
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

        $validTypes    = array_keys(OwnerLead::$propertyTypes);
        $validPurposes = array_keys(OwnerLead::$purposes);
        $validStatuses = array_keys(OwnerLead::$statuses);
        $validSources  = array_keys(OwnerLead::$sources);
        $validLocs     = array_keys(OwnerLead::$locations);

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

            OwnerLead::create([
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

    public function export(Request $request)
    {
        $search  = $request->input('search');
        $status  = $request->input('status');
        $purpose = $request->input('purpose');

        $ownerLeads = OwnerLead::when($search, fn($q) => $q->where(fn($sq) =>
                $sq->where('name', 'like', "%$search%")
                   ->orWhere('mobile', 'like', "%$search%")
                   ->orWhere('email', 'like', "%$search%")
            ))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($purpose, fn($q) => $q->where('purpose', $purpose))
            ->latest()
            ->get();

        $isAr = app()->getLocale() === 'ar';
        $q = fn($v) => '"' . str_replace('"', '""', (string)($v ?? '')) . '"';

        $headers = $isAr
            ? ['الاسم', 'الهاتف', 'البريد', 'الموقع', 'نوع العقار', 'الغرض', 'أدنى ميزانية', 'أقصى ميزانية', 'غرف', 'الحالة', 'المصدر', 'ملاحظات']
            : ['Name', 'Mobile', 'Email', 'Location', 'Property Type', 'Purpose', 'Min Budget', 'Max Budget', 'Bedrooms', 'Status', 'Source', 'Notes'];

        $csv = "\xEF\xBB\xBF" . implode(',', array_map($q, $headers)) . "\r\n";
        foreach ($ownerLeads as $lead) {
            $csv .= implode(',', array_map($q, [
                $lead->name,
                $lead->mobile,
                $lead->email,
                $lead->location,
                $lead->property_type,
                $lead->purpose,
                $lead->min_budget,
                $lead->max_budget,
                $lead->bedrooms,
                $lead->status,
                $lead->source,
                $lead->notes,
            ])) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="owner-leads.csv"',
        ]);
    }
}
