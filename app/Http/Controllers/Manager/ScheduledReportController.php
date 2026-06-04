<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\ScheduledReport;
use App\Services\ScheduledReportRunner;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScheduledReportController extends Controller
{
    public function index(Request $request)
    {
        $section = $request->input('section');

        $reports = ScheduledReport::query()
            ->when($section, fn ($q) => $q->where('section', $section))
            ->with(['property', 'association', 'creator', 'latestRun'])
            ->orderBy('next_run_at')
            ->paginate(20)
            ->withQueryString();

        return view('manager.scheduled-reports.index', compact('reports', 'section'));
    }

    public function create(Request $request)
    {
        $section    = ScheduledReport::SECTION_MANAGEMENT;
        $properties = Property::orderBy('name_ar')->get();

        return view('manager.scheduled-reports.create', compact('section', 'properties'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['period_months'] = (int) $data['period_months'];

        $data['created_by'] = $request->user()->id;
        $data['next_run_at'] = $data['next_run_at'] ?? CarbonImmutable::now()
            ->addMonthsNoOverflow($data['period_months'])->toDateString();

        ScheduledReport::create($data);

        return redirect()
            ->route('manager.scheduled-reports.index', ['section' => $data['section']])
            ->with('success', 'تم إنشاء التقرير المجدول');
    }

    public function edit(ScheduledReport $scheduledReport)
    {
        $properties = Property::orderBy('name_ar')->get();

        return view('manager.scheduled-reports.edit', [
            'report'     => $scheduledReport,
            'section'    => ScheduledReport::SECTION_MANAGEMENT,
            'properties' => $properties,
        ]);
    }

    public function update(Request $request, ScheduledReport $scheduledReport)
    {
        $data = $this->validateData($request);
        $data['period_months'] = (int) $data['period_months'];
        $scheduledReport->update($data);

        return redirect()
            ->route('manager.scheduled-reports.index', ['section' => $scheduledReport->section])
            ->with('success', 'تم تحديث التقرير المجدول');
    }

    public function destroy(ScheduledReport $scheduledReport)
    {
        $scheduledReport->delete();

        return back()->with('success', 'تم حذف التقرير المجدول');
    }

    public function runNow(ScheduledReport $scheduledReport, ScheduledReportRunner $runner)
    {
        $run = $runner->generate($scheduledReport);
        $scheduledReport->advanceSchedule();

        return redirect()
            ->route('manager.scheduled-reports.index', ['section' => $scheduledReport->section])
            ->with('success', 'تم توليد التقرير. الملف: ' . basename($run->file_path));
    }

    public function download(\App\Models\ScheduledReportRun $run, Request $request)
    {
        abort_unless($run->file_path && Storage::disk('local')->exists($run->file_path), 404);

        $absolute = Storage::disk('local')->path($run->file_path);
        $name     = 'hoa-report-' . $run->scheduled_report_id
                    . '-' . ($run->period_start?->format('Y-m') ?? 'period') . '.pdf';

        if ($request->boolean('preview')) {
            return response()->file($absolute, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $name . '"',
            ]);
        }

        return response()->download($absolute, $name, ['Content-Type' => 'application/pdf']);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name'           => ['required', 'string', 'max:160'],
            'section'        => ['required', 'in:hoa,management'],
            'property_id'    => ['nullable', 'exists:properties,id'],
            'association_id' => ['nullable', 'exists:associations,id'],
            'period_months'  => ['required', 'integer', 'min:1', 'max:60'],
            'next_run_at'    => ['nullable', 'date'],
            'recipients'     => ['nullable', 'array'],
            'recipients.*'   => ['email'],
            'status'         => ['required', 'in:active,paused'],
            'notes'          => ['nullable', 'string'],
        ]);
    }
}
