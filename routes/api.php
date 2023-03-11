<?php

use App\Http\Controllers\NewsCategorySubscriptionController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::apiResource(
        'news-category-subscriptions',
        NewsCategorySubscriptionController::class,
        ['except' => ['show', 'update']],
    );
    Route::delete(
        'news-category-subscriptions',
        [NewsCategorySubscriptionController::class, 'destroyAll'],
    )->name('news-category-subscriptions.destroy-all');
});
