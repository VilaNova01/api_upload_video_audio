<?php

use App\Http\Controllers\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudioController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

//upload
Route::post('upload',[FileController::class,'upload']);

//show
Route::get('files',[FileController::class,'show_files']);

//download
Route::get('files/{id}',[FileController::class,'download']);

//thumbnail
Route::get('files/{id}/thumbnail',[FileController::class,'show_thumbnail']);

//converted audio
Route::get('files/{id}/converted',[FileController::class,'show_converted_audio']);
//Route::post('/convert-mp3-to-wav', [AudioController::class, 'convertMp3ToWav']);