<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\inbox;

use Illuminate\Http\Request;

class ChatbaruController extends Controller
{

    // Halaman chat utama
    public function index() {
        //Membuat UserUnix untuk session
        $userUnix = session('user_unix', uniqid('user_', true));
        // $userUnix = session('user_unix', null);

        return view('chat.chat', ['user_unix' => $userUnix]);
    }

    public function store(Request $request)
    {
        try {
            // Ambil userUnix dari session
            $userUnix = session('user_unix', uniqid('user_', true));

            // Validasi input
            $request->validate([
                'message' => 'required|string',
            ]);

            // Simpan pesan ke tabel inbox
            inbox::create([
                'chat_id' => $userUnix,
                'message' => $request->message,
            ]);

            return response()->json(['status' => 'Message saved successfully']);
        } catch (\Exception $e) {
            // Log error
            \Log::error($e->getMessage());
            return response()->json(['status' => 'Error saving message'], 500);
        }
    }
}
