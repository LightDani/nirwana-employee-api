<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

// route testing sederhana
Route::get('/ping', function () {
    return response()->json(['message' => 'API OK']);
});

// route utama untuk employees (controller-nya kita buat di Step 8)
Route::apiResource('employees', EmployeeController::class);
