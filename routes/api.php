<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    echo "Hello World!";
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/user', [UserController::class, 'store']);

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
