<?php

use Modules\SpaceKonnect\Http\Controllers\SpaceKonnectController;
use Illuminate\Support\Facades\Route;


Route::middleware(['web'])->as('spacekonnect.')->group(function () {
    Route::post('update-dospace-settings', [SpaceKonnectController::class, 'updateDospaceSettings'])->name('dospace-settings');
});
