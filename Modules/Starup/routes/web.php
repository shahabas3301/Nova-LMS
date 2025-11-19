<?php

use Modules\Starup\Livewire\Pages\Admin\BadgeList;
use Illuminate\Support\Facades\Route;


Route::middleware(['web','auth','enabled:starup','permit-of:can-manage-badges'])->as('badges.')->prefix('badges.')->group(function() {
    Route::get('badge-list', BadgeList::class)->name('badge-list');
});
