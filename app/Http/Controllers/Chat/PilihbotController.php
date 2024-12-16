<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PilihbotController extends Controller
{
    public function index(){
        return view('chatbot/chatbot');
    }
}
