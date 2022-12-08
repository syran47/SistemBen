<?php

use App\Http\Controllers\BahanBaku\BahanBakuController;
use App\Http\Controllers\ManagementBarangController;
use App\Http\Controllers\Produk\ProdukController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
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

Auth::routes();
Route::post('/login/user',          [UserController::class, 'login'])->name('login.user');

Route::group(['middleware' => 'Admin'], function () {
    Route::get('users/getData',      [UserController::class, 'getDataUser'])->name('getdatauser');
    Route::post('users/update',      [UserController::class, 'update'])->name('users.update');
    Route::post('users/destroy',     [UserController::class, 'destroy'])->name('users.destroy');
    Route::resource('users',         UserController::class)->except(['edit', 'update', 'destroy']);
});

Route::group(['middleware' => 'Pegawai'], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('manajemen/bahanbaku/getData',           [BahanBakuController::class, 'getDataBahan'])->name('getdatabahan');
    Route::get('manajemen/bahanbaku/history/masuk',     [BahanBakuController::class, 'masuk'])->name('history.bahanbaku.masuk');
    Route::get('manajemen/bahanbaku/history/keluar',    [BahanBakuController::class, 'keluar'])->name('history.bahanbaku.keluar');
    Route::post('manajemen/bahanbaku/updatebahan',      [BahanBakuController::class, 'updateBahanBaku'])->name('bahanbaku.updatedata');
    Route::post('manajemen/bahanbaku/destroy',          [BahanBakuController::class, 'destroy'])->name('bahanbaku.destroy');
    Route::get('manajemen/bahanbaku/history/data',      [BahanBakuController::class, 'history'])->name('history.bahanbaku.data');
    Route::resource('manajemen/bahanbaku',              BahanBakuController::class)->only(['index', 'store', 'update']);

    Route::get('manajemen/produk/getData',           [ProdukController::class, 'getDataProduk'])->name('getdataproduk');
    Route::get('manajemen/produk/history/masuk',     [ProdukController::class, 'masuk'])->name('history.produk.masuk');
    Route::get('manajemen/produk/history/keluar',    [ProdukController::class, 'keluar'])->name('history.produk.keluar');
    Route::post('manajemen/produk/updateproduk',     [ProdukController::class, 'updateProduk'])->name('produk.updatedata');
    Route::post('manajemen/produk/destroy',          [ProdukController::class, 'destroy'])->name('produk.destroy');
    Route::get('manajemen/produk/history/data',      [ProdukController::class, 'history'])->name('history.produk.data');
    Route::resource('manajemen/produk',              ProdukController::class)->only(['index', 'store', 'update']);
});

Auth::routes(['register' => 'false']);
