<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API OK']);
});

Route::apiResource('employees', EmployeeController::class);
