<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatbaruController extends Controller
{

    // Halaman chat utama
    public function index() {
        $userUnix = session('user_unix', null);

        return view('chat.chat');
    }
}
