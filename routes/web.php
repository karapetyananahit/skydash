<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\GoogleController;




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
    if(Auth::check()){
        return view('dashboard');
    }
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/delete-image', [ProfileController::class, 'deleteImage'])->name('profile.image.delete');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::get('/users/form/{id?}', [UserController::class, 'form'])->name('user.form');
    Route::post('/users/create', [UserController::class, 'store'])->name('user.store');
    Route::put('/users/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/users/delete/{id}', [UserController::class, 'delete'])->name('user.delete');
    Route::get('/users/cancel', [UserController::class, 'cancel'])->name('user.cancel');
    Route::post('/user/{id}/upload-image', [UserController::class, 'uploadImage'])->name('user.uploadImage');
});

Route::get('auth/facebook', [FacebookController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('auth/facebook/callback', [FacebookController::class, 'handleFacebookCallback']);


Route::get('login/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('login/google/callback', [GoogleController::class, 'handleGoogleCallback']);

require __DIR__.'/auth.php';
