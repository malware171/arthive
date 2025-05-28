<?php

use App\Controllers\AuthController;
use App\Controllers\ClientController;
use App\Controllers\ArtistController;
use App\Controllers\ArtworkController;
use App\Controllers\HomeController;
use Core\Router\Route;

Route::get('/', [AuthController::class, 'checkLogin'])->name('auth.check');
Route::get('/login', [AuthController::class, 'new'])->name('users.login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('users.authenticate');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('users.logout');

    Route::middleware('client')->group(function () {
        Route::get('/client', [ClientController::class, 'index'])->name('client.index');
    });

    Route::middleware('artist')->group(function() {
        Route::get('/adminPage', [ArtistController::class, 'index'])->name('artist.index');
        Route::get('/createPost', [ArtworkController::class, 'new'])->name('artist.new');
        Route::post('/createPost', [ArtworkController::class, 'newArtwork'])->name('artist.newArtwork');
    });
});
