<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

// Redireciona /login para o login do Filament
Route::get('/login', function () {
    return redirect('/home/login');
})->name('login');

// Redireciona /home para o dashboard do Filament
// Painel agora está em /home, então manter /home redirecionando para a raiz do painel
Route::get('/home', function () {
    return redirect('/home');
})->middleware('auth');

Route::get('/', function () {
    if (Auth::check()) {
    return redirect('/home');
    }
    // Página inicial pública (landing)
    return view('landing');
});
