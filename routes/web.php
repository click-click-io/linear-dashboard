<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetricsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/issues', [MetricsController::class, 'showIssues']);

Route::get('/api/metrics', [MetricsController::class, 'getMetrics']);
