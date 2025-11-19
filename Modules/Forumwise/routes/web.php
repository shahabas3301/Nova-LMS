<?php

use Illuminate\Support\Facades\Route;
use Modules\Forumwise\Http\Controllers\ForumwiseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['web','auth','enabled:forumwise', 'verified' , 'locale', 'maintenance'])->group(function() {
    Route::get('forums', [ForumwiseController::class, 'index'])->name('forums');

    Route::get('forum/{slug}', [ForumwiseController::class, 'fetchTopic'])->name('forum-topics');
});

Route::middleware(['web','auth','role:admin|sub_admin','enabled:forumwise', 'verified' , 'locale', 'maintenance','permit-of:can-manage-forums'])->group(function() {
    Route::get('categories', [ForumwiseController::class, 'categories'])->name('categories');
});

Route::middleware(['web','enabled:forumwise', 'locale', 'maintenance'])->group(function() {
    Route::get('topic/{slug}', [ForumwiseController::class, 'fetchPost'])->name('topic');

});