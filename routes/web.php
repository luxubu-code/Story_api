    <?php

    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\WEB\CommentWebController;
    use App\Http\Controllers\Web\FavoriteWebController;
    use App\Http\Controllers\WEB\RatingWebController;
    use App\Http\Controllers\WEB\StoryWebController;
    use App\Http\Controllers\WEB\UserWebController;
    use App\Http\Controllers\WEB\ChapterWebController;
    use App\Http\Controllers\API\CommentController;
    use App\Http\Controllers\API\RatingController;
    use App\Http\Controllers\WEB\ImageWebController;
    use App\Http\Controllers\WEB\VipSubscriptionStatsController;
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
        Route::post('/{story_id}/update', [StoryWebController::class, 'update'])->name('update');
        Route::delete('/chapter/{id}', [StoryWebController::class, 'destroyChapter'])->name('chapters.destroy');
        Route::delete('/{id}', [StoryWebController::class, 'destroyStory'])->name('destroy');
        Route::get('/search', [StoryWebController::class, 'searchStory'])->name('search');
    });
    // Route::prefix('favourite')->name('favourite.')->group(
    //     function () {
    //         Route::get('/favourite', [StoryWebController::class, 'index'])->name('index');
    //     }
    // );
    Route::get('most', [StoryWebController::class, 'getMost'])->name('favorite.most');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserWebController::class, 'index'])->name('index');
        Route::put('/{user}', [UserWebController::class, 'update'])->name('update');
    });

    // Chapter Routes
    Route::prefix('chapters')->name('chapters.')->group(function () {
        Route::get('/{chapter}/images', [ImageWebController::class, 'show'])->name('images');
        // Route::get('/{chapter_id}/images', [ImageWebController::class, 'show'])->name('images.show');
    });
    Route::get('/images/{image}/edit', [ImageWebController::class, 'edit'])->name('images.edit');
    Route::put('/images/{image}', [ImageWebController::class, 'update'])->name('images.update');
    Route::delete('/images/{image}', [ImageWebController::class, 'destroy'])->name('images.destroy');

    // In routes/web.php or routes/api.php
    Route::get('/stories/{story_id}/comments', [CommentController::class, 'index']);
    Route::get('/stories/{story_id}/ratings', [RatingController::class, 'index']);
    Route::prefix('admin')->group(function () {});


    Route::get('/admin', [VipSubscriptionStatsController::class, 'index'])->name('admin.index');


    Route::get('/comments', [CommentWebController::class, 'getAll'])->name('comment.index');
    Route::delete('/comments-web/{id}', [CommentWebController::class, 'deleteComment'])->name('comment.delete');

    Route::get('/ratings', [RatingWebController::class, 'getAll'])->name('rating.index');
    Route::delete('/rating-web/{id}', [RatingWebController::class, 'deleteRating'])->name('rating.delete');

    require __DIR__ . '/auth.php';