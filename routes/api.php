<?php

use App\Http\Controllers\AdsImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth')->prefix('ads-image')->name('ads-image.')->group(function () {

});
