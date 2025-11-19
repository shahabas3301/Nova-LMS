<?php


use Illuminate\Support\Facades\Route;
use Modules\Forumwise\Http\Controllers\ForumwiseApiController;
use Modules\Forumwise\Http\Controllers\ForumsApiController;
use Modules\Forumwise\Http\Controllers\TopicsApiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'forumwise'], function () {
    Route::get('categories',   [ForumwiseApiController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('forums',                    [ForumsApiController::class, 'index']);
        Route::get('topics',                    [TopicsApiController::class, 'index']);
        Route::get('topic/{slug}',              [TopicsApiController::class, 'getTopicDetail']);
        Route::get('topic-contributors/{slug}', [TopicsApiController::class, 'getTopicContributors']);
        Route::post('create-topic',             [TopicsApiController::class, 'createTopic']);
        Route::get('popular-topics',            [TopicsApiController::class, 'getPopularTopics']);
        Route::get('related-topics/{slug}',     [TopicsApiController::class, 'getRelatedTopic']);
        Route::get('top-users',                 [TopicsApiController::class, 'getTopUsers']);

        Route::get('popular-topics-media',      [TopicsApiController::class, 'getPopularTopicsMedia']);
        Route::get('top-user-media',            [TopicsApiController::class, 'getTopUserMedia']);

        Route::get('comments/{topicId}',        [TopicsApiController::class, 'getComments']);
        Route::post('reply',                    [TopicsApiController::class, 'addReply']);
        Route::post('vote',                     [TopicsApiController::class, 'addVote']);
    });
});



