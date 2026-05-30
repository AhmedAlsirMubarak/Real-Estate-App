<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\RentalContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RentalContractController extends Controller
{
    public function uploadFile(Request $request, RentalContract $contract)
    {
        $request->validate([
            'contract_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($contract->contract_file) {
            Storage::disk('public')->delete($contract->contract_file);
        }

        $file = $request->file('contract_file');
        if (! $file->isValid()) {
            $msg = app()->getLocale() === 'ar' ? 'فشل تحميل الملف. حاول مرة أخرى.' : 'Failed to upload file. Please try again.';
            return back()->with('error', $msg);
        }

        $filename = 'contract_' . $contract->id . '_' . time() . '.' . ($file->getClientOriginalExtension() ?: 'pdf');

        $targetDir = storage_path('app/public/contracts/' . $contract->id);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $moved = move_uploaded_file($file->getPathname(), $targetDir . DIRECTORY_SEPARATOR . $filename);

        if (!$moved) {
            $msg = app()->getLocale() === 'ar' ? 'فشل حفظ الملف.' : 'Failed to save file.';
            return back()->with('error', $msg);
        }

        $path = 'contracts/' . $contract->id . '/' . $filename;

        $contract->update(['contract_file' => $path]);

        $msg = app()->getLocale() === 'ar' ? 'تم رفع ملف العقد بنجاح.' : 'Contract file uploaded successfully.';
        return back()->with('success', $msg);
    }

    public function deleteFile(RentalContract $contract)
    {
        if ($contract->contract_file) {
            Storage::disk('public')->delete($contract->contract_file);
            $contract->update(['contract_file' => null]);
        }

        $msg = app()->getLocale() === 'ar' ? 'تم حذف ملف العقد.' : 'Contract file deleted.';
        return back()->with('success', $msg);
    }
}
