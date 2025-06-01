<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use App\Models\Book;





Route::get('/', function () {
    return redirect()->route('books.index');
});

Route::resource('books', BookController::class)
    ->only(['index', 'show' , 'create' , 'store']);

// Route::resource('books.reviews', ReviewController::class)
//     ->scoped(['review' => 'book'])
//     ->only(['create', 'store']);
    // ->middleware('throttle:reviews'); 

Route::get('books/{book}/reviews/create', [ReviewController::class, 'create'])
    ->name('books.reviews.create');

Route::post('books/{book}/reviews', [ReviewController::class, 'store'])
    ->name('books.reviews.store')
    ->middleware('throttle:reviews');

    // php artisan cache:clear   php artisan config:clear php artisan config:cache    ------------to clear rate limitter


    
