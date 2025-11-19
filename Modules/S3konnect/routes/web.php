<?php

use Modules\S3konnect\Http\Controllers\S3KonnectController;
use Illuminate\Support\Facades\Route;


Route::middleware(['web'])->as('s3konnect.')->group(function () {
    Route::post('update-s3bucket-settings', [S3KonnectController::class, 'updateS3BucketSettings'])->name('settings');
});
