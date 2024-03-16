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

// Route::get('/', function () {
//     return view('index');
// });

Route::group(['namespace' => 'App\Http\Controllers'], function()
{   
    /**
     * Home Routes
     */
    Route::get('/', 'HomeController@index')->name('home.index');

    Route::group(['middleware' => ['guest']], function() {
        /**
         * Register Routes
         */
        Route::get('/register', 'RegisterController@show')->name('register.show');
        Route::post('/register', 'RegisterController@register')->name('register.perform');

        /**
         * Login Routes
         */
        Route::get('/login', 'LoginController@show')->name('login.show');
        Route::post('/login', 'LoginController@login')->name('login.perform');

    });

    Route::group(['middleware' => ['auth', 'permission']], function() {
        /**
         * Logout Routes
         */
        Route::get('/logout', 'LogoutController@perform')->name('logout.perform');

        /**
         * User Routes
         */
        Route::group(['prefix' => 'users'], function() {
            Route::get('/', 'UsersController@index')->name('users.index');
            Route::get('/create', 'UsersController@create')->name('users.create');
            Route::post('/create', 'UsersController@store')->name('users.store');
            Route::get('/{user}/show', 'UsersController@show')->name('users.show');
            Route::get('/{user}/edit', 'UsersController@edit')->name('users.edit');
            Route::patch('/{user}/update', 'UsersController@update')->name('users.update');
            Route::delete('/{user}/delete', 'UsersController@destroy')->name('users.destroy');
        });

        /**
         * User Routes
         */
        Route::group(['prefix' => 'posts'], function() {
            Route::get('/', 'PostsController@index')->name('posts.index');
            Route::get('/create', 'PostsController@create')->name('posts.create');
            Route::post('/create', 'PostsController@store')->name('posts.store');
            Route::get('/{post}/show', 'PostsController@show')->name('posts.show');
            Route::get('/{post}/edit', 'PostsController@edit')->name('posts.edit');
            Route::patch('/{post}/update', 'PostsController@update')->name('posts.update');
            Route::delete('/{post}/delete', 'PostsController@destroy')->name('posts.destroy');
        });

        Route::group(['prefix' => 'produks'], function() {
            Route::get('/', 'ProdukController@index')->name('produks.index');
            Route::post('/store', 'ProdukController@store')->name('produks.store');
            Route::get('/{produk}/show', 'ProdukController@show')->name('produks.show');
            Route::get('/{produk}/edit', 'ProdukController@edit')->name('produks.edit');
            Route::patch('/{produk}/update', 'ProdukController@update')->name('produks.update');
            Route::get('/{produk}/delete', 'ProdukController@destroy')->name('produks.destroy');
        });

        Route::group(['prefix' => 'tipe_produks'], function() {
            Route::get('/', 'TipeProdukController@index')->name('tipe_produk.index');
            Route::post('/store', 'TipeProdukController@store')->name('tipe_produk.store');
            Route::get('/{tipe_produk}/show', 'TipeProdukController@show')->name('tipe_produk.show');
            Route::get('/{tipe_produk}/edit', 'TipeProdukController@edit')->name('tipe_produk.edit');
            Route::patch('/{tipe_produk}/update', 'TipeProdukController@update')->name('tipe_produk.update');
            Route::get('/{tipe_produk}/delete', 'TipeProdukController@destroy')->name('tipe_produk.destroy');
        });

        Route::group(['prefix' => 'kondisi'], function() {
            Route::get('/', 'KondisiController@index')->name('kondisi.index');
            Route::post('/store', 'KondisiController@store')->name('kondisi.store');
            Route::get('/{kondisi}/show', 'KondisiController@show')->name('kondisi.show');
            Route::get('/{kondisi}/edit', 'KondisiController@edit')->name('kondisi.edit');
            Route::patch('/{kondisi}/update', 'KondisiController@update')->name('kondisi.update');
            Route::get('/{kondisi}/delete', 'KondisiController@destroy')->name('kondisi.destroy');
        });

        Route::group(['prefix' => 'tipe_lokasi'], function() {
            Route::get('/', 'TipeLokasiController@index')->name('tipe_lokasi.index');
            Route::post('/store', 'TipeLokasiController@store')->name('tipe_lokasi.store');
            Route::get('/{tipe_lokasi}/show', 'TipeLokasiController@show')->name('tipe_lokasi.show');
            Route::get('/{tipe_lokasi}/edit', 'TipeLokasiController@edit')->name('tipe_lokasi.edit');
            Route::patch('/{tipe_lokasi}/update', 'TipeLokasiController@update')->name('tipe_lokasi.update');
            Route::get('/{tipe_lokasi}/delete', 'TipeLokasiController@destroy')->name('tipe_lokasi.destroy');
        });

        Route::group(['prefix' => 'lokasi'], function() {
            Route::get('/', 'LokasiController@index')->name('lokasi.index');
            Route::post('/store', 'LokasiController@store')->name('lokasi.store');
            Route::get('/{lokasi}/show', 'LokasiController@show')->name('lokasi.show');
            Route::get('/{lokasi}/edit', 'LokasiController@edit')->name('lokasi.edit');
            Route::patch('/{lokasi}/update', 'LokasiController@update')->name('lokasi.update');
            Route::get('/{lokasi}/delete', 'LokasiController@destroy')->name('lokasi.destroy');
        });

        Route::resource('roles', 'RolesController');
        Route::resource('permissions', 'PermissionsController');
    });
});
