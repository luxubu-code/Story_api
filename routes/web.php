<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WEB\StoryWebController;
use Illuminate\Support\Facades\Route;

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

Route::get('/stories', [StoryWebController::class, 'showAll'])->name('stories.index');
Route::post('/stories', [StoryWebController::class, 'store'])->name('stories.store');
Route::get('/stories/{story_id}', [StoryWebController::class, 'show'])->name('stories.show');
Route::post('/stories/{story_id}/upload', [StoryWebController::class, 'upload'])->name('stories.upload');
Route::delete('/stories/chapter/{id}', [StoryWebController::class, 'destroyChapter'])->name('chapters.destroy');
Route::delete('/stories/{id}', [StoryWebController::class, 'destroyStory'])->name('stories.destroy');
Route::get('/search', [StoryWebController::class, 'searchStory'])->name('stories.search');
Route::prefix('admin')->group(function () {


});

require __DIR__ . '/auth.php';
