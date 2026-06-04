<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;

trait StoresUploadedFiles
{
    protected function storeUploadedFile(UploadedFile $file, string $directory): string|false
    {
        if (! $file->isValid()) {
            return false;
        }

        $ext      = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = sha1(uniqid('', true) . microtime()) . '.' . $ext;
        $dir      = public_path('storage' . DIRECTORY_SEPARATOR . $directory);
        $dest     = $dir . DIRECTORY_SEPARATOR . $filename;

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        try {
            $file->move($dir, $filename);
            if (file_exists($dest)) {
                return $directory . '/' . $filename;
            }
        } catch (\Throwable) {}

        // fallback: raw copy from temp path
        $tmp = $file->getRealPath() ?: '';
        if ($tmp && file_exists($tmp) && copy($tmp, $dest)) {
            return $directory . '/' . $filename;
        }

        return false;
    }
}
