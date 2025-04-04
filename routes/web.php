<?php

use App\Exports\InfluencersExport;
use App\Http\Controllers\InfluencerController;
use App\Http\Controllers\InfluencersCardsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\GoogleController;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

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

    Route::get('/influencers', [InfluencerController::class, 'index'])->name('influencer.index');
    Route::get('/influencers/form/{id?}', [InfluencerController::class, 'form'])->name('influencer.form');
    Route::post('/influencers/create', [InfluencerController::class, 'store'])->name('influencer.store');
    Route::put('/influencers/update/{id}', [InfluencerController::class, 'update'])->name('influencer.update');
    Route::post('/influencers/{id}/upload-image', [InfluencerController::class, 'uploadImage'])->name('influencer.uploadImage');
    Route::post('/influencer/delete-image', [InfluencerController::class, 'deleteImage'])->name('influencer.deleteImage');
    Route::delete('/influencers/delete/{id}', [InfluencerController::class, 'delete'])->name('influencer.delete');
    Route::post('/influencers/cancel', [InfluencerController::class, 'cancel'])->name('influencer.cancel');


    Route::get('/influencers-cards', [InfluencersCardsController::class, 'index'])->name('influencers-cards.index');
    Route::get('/influencers-cards/search', [InfluencersCardsController::class, 'search'])->name('influencers-cards.search');
    Route::get('/influencers-cards/load-more', [InfluencersCardsController::class, 'loadMore'])->name('influencers-cards.loadMore');

    Route::post('/export-influencers', [InfluencerController::class, 'queueExport'])->name('export.influencers');
    Route::get('/download-export/{filename}', [InfluencerController::class, 'downloadExport'])->name('download.influencers');

});

Route::get('auth/facebook', [FacebookController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('auth/facebook/callback', [FacebookController::class, 'handleFacebookCallback']);


Route::get('login/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('login/google/callback', [GoogleController::class, 'handleGoogleCallback']);

require __DIR__.'/auth.php';
