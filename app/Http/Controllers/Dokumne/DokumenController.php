<?php

namespace App\Http\Controllers\Dokumne;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx|max:2048', // Validasi tipe file dan ukuran maksimal
        ]);

        // Simpan dokumen ke dalam storage
        if ($file = $request->file('document')) {
            $path = $file->store('documents');
            return response()->json(['success' => true, 'path' => $path]);
        }

        return response()->json(['success' => false]);
    }
}
