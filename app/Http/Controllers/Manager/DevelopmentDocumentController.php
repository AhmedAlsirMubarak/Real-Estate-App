<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentDocument;
use App\Models\DevelopmentProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DevelopmentDocumentController extends Controller
{
    public function store(Request $request, DevelopmentProject $development)
    {
        $request->validate([
            'type'  => 'required|in:contract,invoice,other',
            'title' => 'required|string|max:255',
            'file'  => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $file      = $request->file('file');
        $original  = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $filename  = Str::uuid() . ($extension ? '.' . $extension : '');
        $destDir   = storage_path('app/public/development/documents');

        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        $file->move($destDir, $filename);
        $path = 'development/documents/' . $filename;

        $development->documents()->create([
            'type'          => $request->type,
            'title'         => $request->title,
            'file_path'     => $path,
            'original_name' => $original,
        ]);

        return redirect()->route('manager.development.show', $development)
            ->with('success', app()->getLocale() === 'ar' ? 'تم رفع المستند بنجاح.' : 'Document uploaded successfully.');
    }

    public function destroy(DevelopmentProject $development, DevelopmentDocument $document)
    {
        abort_if($document->development_project_id !== $development->id, 404);
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return back()->with('success', app()->getLocale() === 'ar' ? 'تم حذف المستند.' : 'Document removed.');
    }
}
