<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WEB\StoryWebController;
use App\Http\Controllers\WEB\UserWebController;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\get;

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

Route::prefix('stories')->name('stories.')->group(function () {
    Route::get('/', [StoryWebController::class, 'showAll'])->name('index');
    Route::post('/', [StoryWebController::class, 'store'])->name('store');
    Route::get('/{story_id}', [StoryWebController::class, 'show'])->name('show');
    Route::post('/{story_id}/upload', [StoryWebController::class, 'upload'])->name('upload');
    Route::delete('/chapter/{id}', [StoryWebController::class, 'destroyChapter'])->name('chapters.destroy');
    Route::delete('/{id}', [StoryWebController::class, 'destroyStory'])->name('destroy');
    Route::get('/search', [StoryWebController::class, 'searchStory'])->name('search');
});
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserWebController::class, 'index'])->name('index');
});


Route::prefix('admin')->group(function () {});

require __DIR__ . '/auth.php';
