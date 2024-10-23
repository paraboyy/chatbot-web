<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ChatbotController extends Controller
{
    // Fungsi untuk membaca file JSON
    private function readMessagesFromFile() {
        $filePath = storage_path('app/chat_messages.json');
        
        if (File::exists($filePath)) {
            return json_decode(File::get($filePath), true) ?? [];
        }

        return [];
    }

    // Fungsi untuk menulis ke file JSON
    private function writeMessagesToFile($messages) {
        // Filter pesan untuk menghapus pesan yang bernilai null
        $filteredMessages = array_filter($messages, function($message) {
            return !is_null($message);
        });

        // Dapatkan jalur file
        $filePath = storage_path('app/chat_messages.json');

        // Hanya simpan jika ada pesan yang valid
        if (!empty($filteredMessages)) {
            File::put($filePath, json_encode($filteredMessages, JSON_PRETTY_PRINT));
        }
    }

    // Halaman chat utama
    public function index() {
        $userUnix = session('user_unix', null);
        $messages = $this->readMessagesFromFile();

        return view('chat', [
            'user_unix' => $userUnix,
            'messages' => $userUnix ? ($messages[$userUnix] ?? []) : []
        ]);
    }

    // Menangani pengiriman pesan dari user
    public function sendMessage(Request $request) {
        $messages = $this->readMessagesFromFile();
        $userUnix = session('user_unix', uniqid('user_', true)); // Set userUnix di session jika belum ada

        $userMessage = $request->input('message');

        // Pastikan pesan tidak kosong atau null sebelum menyimpannya
        if (!empty($userMessage)) {
            // Simpan pesan yang dikirim oleh user
            $messages[$userUnix][] = [
                'type' => 'user', 
                'message' => $userMessage,
                'date' => now()->toDateTimeString()
            ];
        }

        // Simpan ke file JSON
        $this->writeMessagesToFile($messages);

        session(['user_unix' => $userUnix]); // Pastikan userUnix tersimpan di session

        return response()->json(['user_unix' => $userUnix, 'messages' => $messages[$userUnix] ?? []]);
    }

    // Menangani balasan dari CS
    public function csReply(Request $request) {
        $messages = $this->readMessagesFromFile();
        $userUnix = $request->input('user_unix');
        $csMessage = $request->input('message');

        if (isset($messages[$userUnix]) && !empty($csMessage)) {
            // Simpan pesan balasan dari CS
            $messages[$userUnix][] = [
                'type' => 'cs', 
                'message' => $csMessage,
                'date' => now()->toDateTimeString()
            ];

            // Simpan ke file JSON
            $this->writeMessagesToFile($messages);

            return response()->json(['messages' => $messages[$userUnix]]);
        }

        return response()->json(['messages' => []]);
    }

    // Halaman untuk CS melihat semua pesan
    public function csView() {
        $messages = $this->readMessagesFromFile();
        return view('cs', ['messages' => $messages]);
    }

    // Menghapus chat berdasarkan user_unix
    public function deleteChat(Request $request) {
        $messages = $this->readMessagesFromFile();
        $userUnix = $request->input('user_unix');

        if (isset($messages[$userUnix])) {
            unset($messages[$userUnix]); // Hapus pesan user dari file
            $this->writeMessagesToFile($messages);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
    
    // Halaman untuk memuat pesan berdasarkan user_unix
    public function loadMessages(Request $request) {
        $messages = $this->readMessagesFromFile();
        $userUnix = $request->input('user_unix');

        // Mengambil pesan untuk user tertentu
        return response()->json(['messages' => $messages[$userUnix] ?? []]);
    }
}
