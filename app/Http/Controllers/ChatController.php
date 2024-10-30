<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Menampilkan halaman chatbot
    public function index()
    {
        // Generate unique session id for the user
        if (!Session::has('user_id')) {
            Session::put('user_id', uniqid('user_', true));
        }
    
        return view('chatbot');
    }

    // Menampilkan halaman customer service
    public function chat(Request $request)
    {
        $userMessage = $request->input('message');

        // Mendapatkan token dari .env
        $botToken = env('CHATBOT_TOKEN');
    
        // URL untuk mengirim pesan ke Telegram
        $url = "https://api.telegram.org/bot$botToken/sendMessage";
    
        // Mempersiapkan data untuk dikirim ke Telegram
        $data = [
            'chat_id' => '1898296743', // Ganti dengan ID chat CS atau grup yang digunakan oleh CS
            'text' => "Pesan dari pengguna: $userMessage", // Menambahkan informasi bahwa ini adalah pesan dari pengguna
        ];
    
        // Mengirim permintaan POST ke API Telegram
        $response = Http::post($url, $data);
    
        if ($response->successful()) {
            return response()->json(['response' => "Pesan Anda telah dikirim ke customer service. Tunggu balasan dari CS."]);
        } else {
            return response()->json(['response' => 'Gagal mengirim pesan ke customer service.']);
        }
    }

    public function webook(Request $request) {
        // Mengambil data yang dikirim oleh Telegram
        $update = $request->input();

        // Mengecek apakah ada pesan baru dari CS
        if (isset($update['message']) && isset($update['message']['text'])) {
            $csMessage = $update['message']['text'];

            // Kirim pesan CS ke website
            Session::flash('cs_response', $csMessage);
        }

        return response()->json(['status' => 'success']);
    }
}
