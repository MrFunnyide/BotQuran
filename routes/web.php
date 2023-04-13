<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', [\App\Http\Controllers\test::class, 'testAyat'])->name('test');

Route::get('daftarTranslator', [\App\Http\Controllers\translator::class, 'daftarTranslator'])->name('daftar');

Route::get('daftarLanguage', [\App\Http\Controllers\language::class, 'daftarLanguage'])->name('daftarLanguage');


