<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoryController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
    
// Route quản lý truyện
Route::middleware('auth')->group(function () {
    Route::get('/admin/stories', [StoryController::class, 'adminIndex'])->name('admin.stories');
    Route::get('/admin/stories/create', [StoryController::class, 'createW'])->name('stories.create');
    Route::post('/admin/stories', [StoryController::class, 'storeW'])->name('stories.store');
    Route::get('/admin/stories/{id}/edit', [StoryController::class, 'editW'])->name('stories.edit');
    Route::put('/admin/stories/{id}', [StoryController::class, 'updateW'])->name('stories.update');
    Route::delete('/admin/stories/{id}', [StoryController::class, 'destroyW'])->name('stories.destroy');
});


require __DIR__ . '/auth.php';
