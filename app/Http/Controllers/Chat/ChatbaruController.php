<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
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
}
