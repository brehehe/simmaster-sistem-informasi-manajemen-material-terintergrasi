<?php

namespace App\Http\Controllers;

use App\Models\Regulation\Regulation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegulationController extends Controller
{
    public function download($id)
    {
        $regulation = Regulation::findOrFail($id);

        if (!$regulation->file_path || !Storage::disk('public')->exists($regulation->file_path)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        // Increment download count
        $regulation->increment('download_count');

        $filePath = Storage::disk('public')->path($regulation->file_path);
        $fileName = $regulation->file_name ?: basename($filePath);

        return response()->download($filePath, $fileName);
    }
}
