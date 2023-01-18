<?php

use App\Http\Controllers\FileManager\MediaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/image/{id}', [HomeController::class, 'showImage'])->name('image.view');
Route::post('/', [HomeController::class, 'upload'])->name('upload');
Route::delete('/', [HomeController::class, 'remove'])->name('remove');


// File Upload Input Test Route
Route::get('/file/upload', [UploadController::class, 'index'])->name('file.index');
Route::post('/file/upload', [UploadController::class, 'upload']);

Route::prefix('/media-manager')->middleware(['web'])->as('media.')->group(function () {
    Route::redirect('/', 'file/upload', 301);;
    Route::get('/files/get', [MediaController::class, 'filter'])->name('files.get');
    Route::get('/files/filter', [MediaController::class, 'filter'])->name('files.filter');
    Route::get('/file/{id}/preview', [MediaController::class, 'filePreview']);
    Route::get('/image/{id?}', [MediaController::class, 'showImage'])->name('image.view');
    Route::get('/image/{id}/{any?}', [MediaController::class, 'showImage']);
    Route::post('/upload', [MediaController::class, 'upload'])->name('upload');
});
