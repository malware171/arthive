<?php

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use Core\Router\Route;

// HomePage
Route::get('/', [HomeController::class, 'index'])->name('root');

// Authentication
Route::get('/login', [AuthController::class, 'formLogin'])->name('users.login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('users.authenticate');
Route::get('/login', [AuthController::class, 'destroy'])->name('users.logout');