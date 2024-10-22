<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Menampilkan halaman chatbot
    public function index()
    {
        return view('chat');
    }

    // Menampilkan halaman customer service
    public function service()
    {
        return view('service');
    }

    // Mengirim pesan dari pengguna
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Simpan pesan di session
        $messages = session()->get('messages', []);
        $messages[] = [
            'user' => 'User',
            'message' => $request->input('message'),
        ];
        session(['messages' => $messages]);

        return redirect()->route('chat');
    }

    // Mengirim balasan dari customer service
    public function replyMessage(Request $request)
    {
        $request->validate([
            'reply' => 'required|string|max:255',
        ]);

        // Simpan balasan di session
        $messages = session()->get('messages', []);
        $messages[] = [
            'user' => 'Customer Service',
            'message' => $request->input('reply'),
        ];
        session(['messages' => $messages]);

        return redirect()->route('service');
    }

    // Mendapatkan semua pesan
    public function getMessages()
    {
        return session()->get('messages', []);
    }
}
