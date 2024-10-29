<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Chat\ChatbaruController;
use App\Http\Controllers\ChatbotController;

// Route::get('/', function () {
//     return view('welcome');
// });  

Route::get('/', [ChatbaruController::class, 'index']);

// CHATBOT AND CS TANPA TELEGRAM
// Route::get('/', [ChatbotController::class, 'index']);
Route::post('/send-message', [ChatbotController::class, 'sendMessage']);
Route::post('/cs-reply', [ChatbotController::class, 'csReply']);
Route::get('/cs', [ChatbotController::class, 'csView']);
Route::post('/delete-chat', [ChatbotController::class, 'deleteChat']);
Route::get('/load-messages', [ChatbotController::class, 'loadMessages']);
// Route::get('/get-messages', [ChatbotController::class, 'getMessages']);


//CHATBOT PERLU TELEGRAM

Route::get('/bot', [ChatController::class, 'index']);
Route::get('/webhook', [ChatController::class, 'webhook']); // Endpoint untuk menerima pesan dari bot Telegram dan mengirimkannya ke website
Route::get('/chat', [ChatController::class, 'chat']); // Endpoint untuk mengirim pesan dari website ke bot Telegram
     