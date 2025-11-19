<?php

use Illuminate\Support\Facades\Route;
use Modules\CourseBundles\Http\Controllers\Api\CourseBundleController;


Route::prefix('coursebundles')->group(function () {
    Route::get('student/coursebundles',                         [CourseBundleController::class, 'getCourseBundles']);
});

Route::middleware('auth:sanctum')->prefix('coursebundles')->group(function () {
    Route::middleware('role:tutor')->group(function () {
        Route::get('coursebundles-list',                [CourseBundleController::class, 'courseBundlesList']);
        Route::post('publish-coursebundle',             [CourseBundleController::class, 'publishCourseBundle']);
        Route::post('archive-coursebundle',             [CourseBundleController::class, 'archiveCourseBundle']);
        Route::delete('delete-coursebundle/{id}',       [CourseBundleController::class, 'deleteCourseBundle']);
    });
});
