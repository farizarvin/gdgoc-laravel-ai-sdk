<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Chatbot;

Route::redirect('/', '/chat');

Route::get('/chat', Chatbot::class)->name('chat');
