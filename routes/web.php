<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

// Redireciona /login para o login do Filament
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Redireciona /home para o dashboard do Filament
Route::get('/home', function () {
    return redirect('/admin');
})->middleware('auth');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/home');
    }
    return redirect('/login');
});
