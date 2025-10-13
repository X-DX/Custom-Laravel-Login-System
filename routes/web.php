<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;

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
    return view('welcome');
});


Route::get('login', [CustomAuthController::class, 'index'])->name('login');
Route::post('post-login', [CustomAuthController::class, 'postLogin'])->name('login.post'); 
Route::get('registration', [CustomAuthController::class, 'registration'])->name('register');
Route::post('post-registration', [CustomAuthController::class, 'postRegistration'])->name('register.post'); 
Route::get('dashboard', [CustomAuthController::class, 'dashboard']); 
Route::post('logout', [CustomAuthController::class, 'logout'])->name('logout');

Route::get('captcha-refresh', function () {
    return response()->json(['captcha'=> captcha_img()]);
});

Route::post('/force-login', [CustomAuthController::class, 'forceLogin'])->name('force.login');

Route::get('/forgot-password', [CustomAuthController::class, 'showForgotForm']);
Route::post('/forgot-password', [CustomAuthController::class, 'sendResetLink']);

Route::get('/reset-password', [CustomAuthController::class, 'showResetForm']);
Route::post('/reset-password', [CustomAuthController::class, 'resetPassword']);