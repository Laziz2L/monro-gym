<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::resource('trainings', \App\Http\Controllers\API\TrainingController::class);
Route::post('trainings/load', [\App\Http\Controllers\API\TrainingController::class, 'load']);
Route::post('trainings/book', [\App\Http\Controllers\API\TrainingController::class, 'book']);
Route::post('trainings/pop', [\App\Http\Controllers\API\TrainingController::class, 'pop']);
