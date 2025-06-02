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
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');
    Route::get('/home/pages/{page}', [HomeController::class, 'index'])->name('artworks.paginate');

    Route::middleware('artist')->group(function () {
        //SHOW
        Route::get('/admin/artworks', [ArtworkController::class, 'index'])->name('artist.admin.page');

        //CREATE
        Route::get('/admin/artworks/new', [ArtworkController::class, 'new'])->name('artwork.new');
        Route::post('/admin/artworks/new', [ArtworkController::class, 'create'])->name('artist.create');

        //UPDATE
        Route::get('/admin/artworks/{id}/edit', [ArtworkController::class, 'edit'])->name('artwork.edit');
        Route::put('/admin/artworks/{id}', [ArtworkController::class, 'update'])->name('artwork.update');

        //DELETE
        Route::delete('/admin/artworks/{id}', [ArtworkController::class, 'destroy'])->name('artwork.destroy');
    });
});
