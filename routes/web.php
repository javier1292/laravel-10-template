<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->json([
        'status' => 'success', 'message' => 'Welcome to the API'
    ], 200);


});

//FALL BACK ROUTE FOR NO URL FOUND
// Route::fallback(function () {
//     return response()->json([
//         'status' => 'error', 'message' => 'Incorrect Route or not logged'
//     ], 404);
// });
