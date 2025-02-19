<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/home', function () {
    return Inertia::render('Home');
})->middleware(['auth', 'verified'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('post')->group(function () {
        Route::get('/', [PostController::class, 'get'])->name('post.get');
        Route::get('/{id}', [PostController::class, 'find'])->name('post.find');
        Route::post('/', [PostController::class, 'create'])->name('post.create');
        Route::put('/{id}', [PostController::class, 'update'])->name('post.update');
        Route::delete('/{id}', [PostController::class, 'delete'])->name('post.delete');
    });
    
    Route::prefix('comment')->group(function () {
        Route::get('/', [CommentController::class, 'get'])->name('comment.get');
        Route::get('/{id}', [CommentController::class, 'find'])->name('comment.find');
        Route::post('/', [CommentController::class, 'create'])->name('comment.create');
        Route::put('/{id}', [CommentController::class, 'update'])->name('comment.update');
        Route::delete('/{id}', [CommentController::class, 'delete'])->name('comment.delete');
    });
});

require __DIR__.'/auth.php';
