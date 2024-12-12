<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

Route::get('/', function () {
    return view('search');
});

Route::post('/api/search', [SearchController::class, 'search']);
