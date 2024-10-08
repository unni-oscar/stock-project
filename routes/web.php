<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BhavcopyController;


// /*
// |--------------------------------------------------------------------------
// | Web Routes
// |--------------------------------------------------------------------------
// |
// | Here is where you can register web routes for your application. These
// | routes are loaded by the RouteServiceProvider within a group which
// | contains the "web" middleware group. Now create something great!
// |
// */

// Route::get('/', function () {
//     return view('welcome');
// });


// Route::get('/fetch-bhavcopy', [BhavcopyController::class, 'fetchBhavcopy']);
// Route::get('/bhavcopy-report', [BhavcopyController::class, 'showReport'])->name('showReport');

// Route::get('/about', function () {
//     return view('about');
// });
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/about', function () {
    return Inertia::render('About');
});
Route::get('/DeliveryReport', [BhavcopyController::class, 'showReport'])->name('DeliveryReport');
Route::get('/test', [BhavcopyController::class, 'test'])->name('tests');
Route::get('/dummy', [BhavcopyController::class, 'generate']);
