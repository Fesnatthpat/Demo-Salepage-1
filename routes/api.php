<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TambonController;

Route::prefix('address')->group(function () {
    Route::get('/provinces', [TambonController::class, 'getProvinces']);
    Route::get('/amphoes', [TambonController::class, 'getAmphoes']);
    Route::get('/tambons', [TambonController::class, 'getTambons']);
    Route::get('/zipcodes', [TambonController::class, 'getZipcodes']);
});
