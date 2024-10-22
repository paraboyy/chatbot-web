<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\ChatController;


// Route::get('/', function () {
//     return view('welcome');
// });


// CHATBOT AND CS

// Route untuk halaman chat utama
Route::get('/', function () {
    // Ambil semua pesan yang disimpan di session
    $userUnix = session('user_unix', null);
    $messages = session('messages', []);

    // Kirimkan ID user_unix dan pesan ke view
    return view('chat', [
        'user_unix' => $userUnix,
        'messages' => $userUnix ? ($messages[$userUnix] ?? []) : []
    ]);
});

// Route untuk menangani pengiriman pesan dari user
Route::post('/send-message', function (Request $request) {
    $messages = session('messages', []);
    $userUnix = session('user_unix', uniqid('user_', true)); // Set userUnix di session jika belum ada

    // Simpan pesan yang dikirim oleh user
    $messages[$userUnix][] = ['type' => 'user', 'message' => $request->input('message')];
    session(['messages' => $messages]);
    session(['user_unix' => $userUnix]); // Pastikan userUnix tersimpan di session

    // Kembalikan response dengan user_unix dan pesan terbaru
    return response()->json(['user_unix' => $userUnix, 'messages' => $messages[$userUnix]]);
});

// Route untuk menangani balasan dari CS
Route::post('/cs-reply', function (Request $request) {
    $messages = session('messages', []);
    $userUnix = $request->input('user_unix');

    // Cek apakah user_unix ada di session messages
    if (isset($messages[$userUnix])) {
        // Simpan pesan balasan dari CS
        $messages[$userUnix][] = ['type' => 'cs', 'message' => $request->input('message')];
        session(['messages' => $messages]);

        // Kembalikan pesan terbaru untuk user_unix tersebut
        return response()->json(['messages' => $messages[$userUnix]]);
    }

    // Jika user_unix tidak ditemukan, kembalikan pesan kosong
    return response()->json(['messages' => []]);
});

// Route untuk halaman CS melihat semua pesan
Route::get('/cs', function () {
    $messages = session('messages', []);
    return view('cs', ['messages' => $messages]);
});

// Route untuk menghapus pesan pada chatbot
Route::post('/delete-chat', function (Request $request) {
    $messages = session('messages', []);
    $userUnix = $request->input('user_unix');

    if (isset($messages[$userUnix])) {
        unset($messages[$userUnix]); // Hapus pesan user dari session
        session(['messages' => $messages]);
        return response()->json(['success' => true]);
    }

    window.location.reload();

    return response()->json(['success' => false]);
});





//CHATBOT PERLU TELEGRAM

Route::get('/bot', function () {
    // Generate unique session id for the user
    if (!Session::has('user_id')) {
        Session::put('user_id', uniqid('user_', true));
    }

    return view('chatbot');
});

// Endpoint untuk mengirim pesan dari website ke bot Telegram
Route::post('/chat', function (Request $request) {
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
});

// Endpoint untuk menerima pesan dari bot Telegram dan mengirimkannya ke website
Route::post('/webhook', function (Request $request) {
    // Mengambil data yang dikirim oleh Telegram
    $update = $request->input();

    // Mengecek apakah ada pesan baru dari CS
    if (isset($update['message']) && isset($update['message']['text'])) {
        $csMessage = $update['message']['text'];

        // Kirim pesan CS ke website
        Session::flash('cs_response', $csMessage);
    }

    return response()->json(['status' => 'success']);
});
