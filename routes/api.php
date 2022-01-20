<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\AuthController;
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

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */
Route::group(['prefix' => 'auth', 'middleware' => 'api'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('isAuth', [AuthController::class, 'authenticated']);
    Route::post('emailForgot', [AuthController::class, 'sendEmailForgot']);
    Route::post('historic', [AuthController::class, 'historic']);
    Route::get('list-historic', [AuthController::class, 'listHistoric']);

    
    Route::get('cabinet', [CabinetController::class, 'index']);
    Route::post('cabinet', [CabinetController::class, 'store']);
    Route::get('cabinet/{id}', [CabinetController::class, 'show']);
    Route::get('cabinet-name/{name}', [CabinetController::class, 'cabinetName']);
    Route::get('cabinet-edit/{id}', [CabinetController::class, 'edit']);
    Route::put('cabinet-update/{id}', [CabinetController::class, 'update']);
    Route::delete('cabinet-delete/{id}', [CabinetController::class, 'destroy']);


    Route::get('createFolder', [FolderController::class, 'createDirectory']);
Route::get('listFolder', [FolderController::class, 'index']);
Route::get('listFolder/{id}', [FolderController::class, 'show']);
Route::post('createFolder', [FolderController::class, 'store']);
Route::delete('delete-folder/{id}', [FolderController::class, 'deleteFolder']);
Route::delete('delete-folder-cabinet/{id}', [FolderController::class, 'deleteFolderCabinet']);
Route::post('remove-folder', [FolderController::class, 'removeFolder']);
Route::put('update-folder/{id}', [FolderController::class, 'updateFolder']);
Route::post('sendEmail', [FolderController::class, 'sendEmail']);
Route::post('upload', [FolderController::class, 'uploadFile']);
Route::get('download/{path}', [FolderController::class, 'downloadFile']);
Route::get('showPdf/{path}', [FolderController::class, 'showPdf']);
Route::put('update-user/{id}', [AuthController::class, 'updateUser']);
Route::get('list-user', [AuthController::class, 'index']);
Route::get('find-user/{id}', [AuthController::class, 'findUser']);
Route::delete('delete-user/{id}', [AuthController::class, 'deleteUser']);
Route::get('find', [FolderController::class, 'findFolder']);
});

