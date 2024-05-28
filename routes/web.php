<?php

use App\Models\Pembelian;
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
    Route::redirect('/', '/login');
    Route::get('/home', 'HomeController@index')->name('home.index');

    Route::group(['middleware' => ['guest']], function() {
        /**
         * Register Routes
         */
        Route::get('/register', 'RegisterController@show')->name('register.show');
        Route::post('/register', 'RegisterController@register')->name('register.perform');

        /**
         * Login Routes
         */
        Route::get('/login', 'LoginController@show')->name('login');
        Route::post('/login', 'LoginController@login')->name('login.perform');

    });

    Route::group(['middleware' => ['auth', 'permission']], function() {
        /**
         * Logout Routes
         */
        Route::get('/logout', 'LogoutController@perform')->name('logout.perform');
        Route::get('checkPromo', 'PromoController@checkPromo')->name('checkPromo');
        Route::get('getPromo', 'PromoController@getPromo')->name('getPromo');
        Route::get('getProdukTerjual', 'ProdukTerjualController@getProdukTerjual')->name('getProdukTerjual');
        Route::post('addKomponen', 'KomponenProdukTerjualController@addKomponen')->name('addKomponen');

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
            Route::get('/{user}/delete', 'UsersController@destroy')->name('users.destroy');
        });

        /**
         * User Routes
         */
        // Route::group(['prefix' => 'posts'], function() {
        //     Route::get('/', 'PostsController@index')->name('posts.index');
        //     Route::get('/create', 'PostsController@create')->name('posts.create');
        //     Route::post('/create', 'PostsController@store')->name('posts.store');
        //     Route::get('/{post}/show', 'PostsController@show')->name('posts.show');
        //     Route::get('/{post}/edit', 'PostsController@edit')->name('posts.edit');
        //     Route::patch('/{post}/update', 'PostsController@update')->name('posts.update');
        //     Route::delete('/{post}/delete', 'PostsController@destroy')->name('posts.destroy');
        // });

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

        Route::group(['prefix' => 'operasional'], function() {
            Route::get('/', 'OperasionalController@index')->name('operasional.index');
            Route::post('/store', 'OperasionalController@store')->name('operasional.store');
            Route::get('/{operasional}/show', 'OperasionalController@show')->name('operasional.show');
            Route::get('/{operasional}/edit', 'OperasionalController@edit')->name('operasional.edit');
            Route::patch('/{operasional}/update', 'OperasionalController@update')->name('operasional.update');
            Route::get('/{operasional}/delete', 'OperasionalController@destroy')->name('operasional.destroy');
        });

        Route::group(['prefix' => 'lokasi'], function() {
            Route::get('/', 'LokasiController@index')->name('lokasi.index');
            Route::post('/store', 'LokasiController@store')->name('lokasi.store');
            Route::get('/{lokasi}/show', 'LokasiController@show')->name('lokasi.show');
            Route::get('/{lokasi}/edit', 'LokasiController@edit')->name('lokasi.edit');
            Route::patch('/{lokasi}/update', 'LokasiController@update')->name('lokasi.update');
            Route::get('/{lokasi}/delete', 'LokasiController@destroy')->name('lokasi.destroy');
        });

        Route::group(['prefix' => 'supplier'], function() {
            Route::get('/', 'SupplierController@index')->name('supplier.index');
            Route::post('/store', 'SupplierController@store')->name('supplier.store');
            Route::get('/{supplier}/show', 'SupplierController@show')->name('supplier.show');
            Route::get('/{supplier}/edit', 'SupplierController@edit')->name('supplier.edit');
            Route::patch('/{supplier}/update', 'SupplierController@update')->name('supplier.update');
            Route::get('/{supplier}/delete', 'SupplierController@destroy')->name('supplier.destroy');
        });

        Route::group(['prefix' => 'ongkir'], function() {
            Route::get('/', 'OngkirController@index')->name('ongkir.index');
            Route::post('/store', 'OngkirController@store')->name('ongkir.store');
            Route::get('/{ongkir}/show', 'OngkirController@show')->name('ongkir.show');
            Route::get('/{ongkir}/edit', 'OngkirController@edit')->name('ongkir.edit');
            Route::patch('/{ongkir}/update', 'OngkirController@update')->name('ongkir.update');
            Route::get('/{ongkir}/delete', 'OngkirController@destroy')->name('ongkir.destroy');
        });

        Route::group(['prefix' => 'tradisional'], function() {
            Route::get('/', 'ProdukJualController@index')->name('tradisional.index');
            Route::get('/create', 'ProdukJualController@create')->name('tradisional.create');
            Route::post('/store', 'ProdukJualController@store')->name('tradisional.store');
            Route::get('/{tradisional}/show', 'ProdukJualController@show')->name('tradisional.show');
            Route::get('/{tradisional}/edit', 'ProdukJualController@edit')->name('tradisional.edit');
            Route::patch('/{tradisional}/update', 'ProdukJualController@update')->name('tradisional.update');
            Route::get('/{tradisional}/delete', 'ProdukJualController@destroy')->name('tradisional.destroy');
        });
        Route::group(['prefix' => 'gift'], function() {
            Route::get('/', 'ProdukJualController@index')->name('gift.index');
            Route::get('/create', 'ProdukJualController@create')->name('gift.create');
            Route::post('/store', 'ProdukJualController@store')->name('gift.store');
            Route::get('/{gift}/show', 'ProdukJualController@show')->name('gift.show');
            Route::get('/{gift}/edit', 'ProdukJualController@edit')->name('gift.edit');
            Route::patch('/{gift}/update', 'ProdukJualController@update')->name('gift.update');
            Route::get('/{gift}/delete', 'ProdukJualController@destroy')->name('gift.destroy');
        });

        Route::group(['prefix'=> 'customer'], function() {
            Route::get('/', 'CustomerController@index')->name('customer.index');
            Route::post('/store', 'CustomerController@store')->name('customer.store');
            Route::get('/store', 'CustomerController@show')->name('customer.show');
            Route::get('/{customer}/edit', 'CustomerController@edit')->name('customer.edit');
            Route::patch('/{customer}/update', 'CustomerController@update')->name('customer.update');
            Route::get('/{customer}/delete', 'CustomerController@destroy')->name('customer.destroy');
        });

        Route::group(['prefix'=> 'karyawan'], function() {
            Route::get('/', 'KaryawanController@index')->name('karyawan.index');
            Route::post('/store', 'KaryawanController@store')->name('karyawan.store');
            Route::get('/store', 'KaryawanController@show')->name('karyawan.show');
            Route::get('/{karyawan}/edit', 'KaryawanController@edit')->name('karyawan.edit');
            Route::patch('/{karyawan}/update', 'KaryawanController@update')->name('karyawan.update');
            Route::get('/{karyawan}/delete', 'KaryawanController@destroy')->name('karyawan.destroy');
        });

        Route::group(['prefix'=> 'rekening'], function() {
            Route::get('/', 'RekeningController@index')->name('rekening.index');
            Route::post('/store', 'RekeningController@store')->name('rekening.store');
            Route::get('/store', 'RekeningController@show')->name('rekening.show');
            Route::get('/{rekening}/edit', 'RekeningController@edit')->name('rekening.edit');
            Route::patch('/{rekening}/update', 'RekeningController@update')->name('rekening.update');
            Route::get('/{rekening}/delete', 'RekeningController@destroy')->name('rekening.destroy');
        });

        Route::group(['prefix'=> 'akun'], function() {
            Route::get('/', 'AkunController@index')->name('akun.index');
            Route::post('/store', 'AkunController@store')->name('akun.store');
            Route::get('/store', 'AkunController@show')->name('akun.show');
            Route::get('/{akun}/edit', 'AkunController@edit')->name('akun.edit');
            Route::patch('/{akun}/update', 'AkunController@update')->name('akun.update');
            Route::get('/{akun}/delete', 'AkunController@destroy')->name('akun.destroy');
        });

        Route::group(['prefix'=> 'aset'], function() {
            Route::get('/', 'AsetController@index')->name('aset.index');
            Route::post('/store', 'AsetController@store')->name('aset.store');
            Route::get('/store', 'AsetController@show')->name('aset.show');
            Route::get('/{rekening}/edit', 'AsetController@edit')->name('aset.edit');
            Route::patch('/{rekening}/update', 'AsetController@update')->name('aset.update');
            Route::get('/{rekening}/delete', 'AsetController@destroy')->name('aset.destroy');
        });

        Route::group(['prefix'=> 'promo'], function() {
            Route::get('/', 'PromoController@index')->name('promo.index');
            Route::post('/store', 'PromoController@store')->name('promo.store');
            Route::get('/store', 'PromoController@show')->name('promo.show');
            Route::get('/{rekening}/edit', 'PromoController@edit')->name('promo.edit');
            Route::patch('/{rekening}/update', 'PromoController@update')->name('promo.update');
            Route::get('/{rekening}/delete', 'PromoController@destroy')->name('promo.destroy');
        });

        Route::group(['prefix'=> 'roles'], function() {
            Route::get('/', 'RolesController@index')->name('roles.index');
            Route::get('/create', 'RolesController@create')->name('roles.create');
            Route::post('/store', 'RolesController@store')->name('roles.store');
            Route::get('/store', 'RolesController@show')->name('roles.show');
            Route::get('/{role}/edit', 'RolesController@edit')->name('roles.edit');
            Route::patch('/{role}/update', 'RolesController@update')->name('roles.update');
            Route::get('/{role}/delete', 'RolesController@destroy')->name('roles.destroy');
        });

        Route::group(['prefix'=> 'permissions'], function() {
            Route::get('/', 'PermissionsController@index')->name('permissions.index');
            Route::get('/create', 'PermissionsController@create')->name('permissions.create');
            Route::post('/store', 'PermissionsController@store')->name('permissions.store');
            Route::get('/store', 'PermissionsController@show')->name('permissions.show');
            Route::get('/{Permission}/edit', 'PermissionsController@edit')->name('permissions.edit');
            Route::patch('/{Permission}/update', 'PermissionsController@update')->name('permissions.update');
            Route::get('/{Permission}/delete', 'PermissionsController@destroy')->name('permissions.destroy');
        });
        
        Route::group(['prefix' => 'penjualan'], function() {
            Route::get('/', 'PenjualanController@index')->name('penjualan.index');
            Route::get('/create', 'PenjualanController@create')->name('penjualan.create');
            Route::post('/store', 'PenjualanController@store')->name('penjualan.store');
            Route::get('/{penjualan}/show', 'PenjualanController@show')->name('penjualan.show');
            Route::get('/{penjualan}/edit', 'PenjualanController@edit')->name('penjualan.edit');
            Route::get('/{penjualan}/payment', 'PenjualanController@payment')->name('penjualan.payment');
            Route::patch('/{penjualan}/update', 'PenjualanController@update')->name('penjualan.update');
            Route::get('/{penjualan}/delete', 'PenjualanController@destroy')->name('penjualan.destroy');
            Route::post('/storekomponen', 'PenjualanController@store_komponen')->name('komponenpenjulan.store');
            Route::post('/storekomponenmutasi', 'PenjualanController@store_komponen_mutasi')->name('komponenmutasi.store');
            Route::post('/storekomponenretur', 'PenjualanController@store_komponen_retur')->name('komponenretur.store');
        });

        Route::group(['prefix' => 'purchase'], function() {
            Route::get('/pembelian', 'PembelianController@index')->name('pembelian.index');
            Route::get('/pembelian/create', 'PembelianController@create')->name('pembelian.create');
            Route::post('/store_po', 'PembelianController@store_po')->name('pembelianpo.store');
            Route::get('/{datapo}/show', 'PembelianController@show')->name('pembelian.show');
            
            Route::get('/invoice', 'PembelianController@invoice')->name('invoicebeli.index');
            Route::get('/invoice/{type}/{datapo}/createinv', 'PembelianController@createinvoice')->name('invoicebiasa.create');
            Route::post('/store_inv', 'PembelianController@storeinvoice')->name('invoicepo.store');
            Route::get('/{datapo}/edit', 'PembelianController@edit_invoice')->name('invoice.edit');
            Route::put('/update/{idinv}', 'PembelianController@update_invoice')->name('invoice.update');
            Route::patch('/{datapo}/update', 'PembelianController@gambarpo_update')->name('gambarpo.update');
            
            Route::get('/pembelian/createinden', 'PembelianController@createinden')->name('pembelianinden.create');
            Route::post('/pembelian/storeinden', 'PembelianController@store_inden')->name('inden.store');
            Route::get('/createinvinden', 'PembelianController@createinvoiceinden')->name('invoiceinden.create');

            Route::get('/retur', 'PembelianController@index_retur')->name('returbeli.index');
            Route::get('/retur/create', 'PembelianController@create_retur')->name('returbeli.create');

        });

        Route::group(['prefix' => 'kontrak'], function() {
            Route::get('/', 'KontrakController@index')->name('kontrak.index');
            Route::get('/create', 'KontrakController@create')->name('kontrak.create');
            Route::post('/store', 'KontrakController@store')->name('kontrak.store');
            Route::get('/{kontrak}/show', 'KontrakController@show')->name('kontrak.show');
            Route::get('/{kontrak}/edit', 'KontrakController@edit')->name('kontrak.edit');
            Route::patch('/{kontrak}/update', 'KontrakController@update')->name('kontrak.update');
            Route::get('/{kontrak}/delete', 'KontrakController@destroy')->name('kontrak.destroy');
        });

        Route::group(['prefix' => 'dopenjualan'], function() {
            Route::get('/', 'DopenjualanController@index')->name('dopenjualan.index');
            Route::get('{penjualan}/create', 'DopenjualanController@create')->name('dopenjualan.create');
            Route::post('/store', 'DopenjualanController@store')->name('dopenjualan.store');
            Route::get('/{dopenjualan}/show', 'DopenjualanController@show')->name('dopenjualan.show');
            Route::get('/{dopenjualan}/edit', 'DopenjualanController@edit')->name('dopenjualan.edit');
            Route::patch('/{dopenjualan}/update', 'DopenjualanController@update')->name('dopenjualan.update');
            Route::get('/{dopenjualan}/delete', 'DopenjualanController@destroy')->name('dopenjualan.destroy');
        });

        Route::group(['prefix' => 'pembayaran'], function() {
            Route::get('/', 'PembayaranController@index')->name('pembayaran.index');
            Route::get('/create', 'PembayaranController@create')->name('pembayaran.create');
            Route::post('/store', 'PembayaranController@store')->name('pembayaran.store');
            Route::get('/{pembayaran}/show', 'PembayaranController@show')->name('pembayaran.show');
            Route::get('/{pembayaran}/edit', 'PembayaranController@edit')->name('pembayaran.edit');
            Route::patch('/{pembayaran}/update', 'PembayaranController@update')->name('pembayaran.update');
            Route::get('/{pembayaran}/delete', 'PembayaranController@destroy')->name('pembayaran.destroy');
            Route::post('/store_invpo', 'PembayaranController@store_bayar_po')->name('bayarpo.store');
        });

        Route::group(['prefix' => 'form'], function() {
            Route::get('/', 'FormPerangkaiController@index')->name('form.index');
            Route::get('/create', 'FormPerangkaiController@create')->name('form.create');
            Route::post('/store', 'FormPerangkaiController@store')->name('form.store');
            Route::get('/{form}/show', 'FormPerangkaiController@show')->name('form.show');
            Route::get('/{form}/edit', 'FormPerangkaiController@edit')->name('form.edit');
            Route::patch('/{form}/update', 'FormPerangkaiController@update')->name('form.update');
            Route::get('/{form}/delete', 'FormPerangkaiController@destroy')->name('form.destroy');
        });

        Route::group(['prefix' => 'formpenjualan'], function() {
            Route::get('/', 'FormPerangkaiController@penjualan_index')->name('formpenjualan.index');
            Route::get('/create', 'FormPerangkaiController@penjualan_create')->name('formpenjualan.create');
            Route::post('/store', 'FormPerangkaiController@penjualan_store')->name('formpenjualan.store');
            Route::get('/{formpenjualan}/show', 'FormPerangkaiController@penjualan_show')->name('formpenjualan.show');
            Route::get('/{formpenjualan}/edit', 'FormPerangkaiController@penjualan_edit')->name('formpenjualan.edit');
            Route::patch('/{formpenjualan}/update', 'FormPerangkaiController@penjualan_update')->name('formpenjualan.update');
            Route::get('/{formpenjualan}/delete', 'FormPerangkaiController@penjualan_destroy')->name('formpenjualan.destroy');
        });

        Route::group(['prefix' => 'do_sewa'], function() {
            Route::get('/', 'DeliveryOrderController@index_sewa')->name('do_sewa.index');
            Route::get('/create', 'DeliveryOrderController@create_sewa')->name('do_sewa.create');
            Route::post('/store', 'DeliveryOrderController@store_sewa')->name('do_sewa.store');
            Route::get('/{do_sewa}/show', 'DeliveryOrderController@show_sewa')->name('do_sewa.show');
            Route::get('/{do_sewa}/edit', 'DeliveryOrderController@edit_sewa')->name('do_sewa.edit');
            Route::patch('/{do_sewa}/update', 'DeliveryOrderController@update_sewa')->name('do_sewa.update');
            Route::get('/{do_sewa}/delete', 'DeliveryOrderController@destroy_sewa')->name('do_sewa.destroy');
        });

        Route::group(['prefix' => 'returpenjualan'], function() {
            Route::get('/', 'ReturpenjualanController@index')->name('returpenjualan.index');
            Route::get('{penjualan}/create', 'ReturpenjualanController@create')->name('returpenjualan.create');
            Route::post('/store', 'ReturpenjualanController@store')->name('returpenjualan.store');
            Route::get('/{returpenjualan}/show', 'ReturpenjualanController@show')->name('returpenjualan.show');
            Route::get('/{returpenjualan}/edit', 'ReturpenjualanController@edit')->name('returpenjualan.edit');
            Route::patch('/{returpenjualan}/update', 'ReturpenjualanController@update')->name('returpenjualan.update');
            Route::get('/{returpenjualan}/delete', 'ReturpenjualanController@destroy')->name('returpenjualan.destroy');
        });

        Route::group(['prefix' => 'inven_galeri'], function() {
            Route::get('/', 'InventoryGalleryController@index')->name('inven_galeri.index');
            Route::get('/create', 'InventoryGalleryController@create')->name('inven_galeri.create');
            Route::post('/store', 'InventoryGalleryController@store')->name('inven_galeri.store');
            Route::get('/{inven_galeri}/show', 'InventoryGalleryController@show')->name('inven_galeri.show');
            Route::get('/{inven_galeri}/edit', 'InventoryGalleryController@edit')->name('inven_galeri.edit');
            Route::patch('/{inven_galeri}/update', 'InventoryGalleryController@update')->name('inven_galeri.update');
            Route::get('/{inven_galeri}/delete', 'InventoryGalleryController@destroy')->name('inven_galeri.destroy');
        });

        Route::group(['prefix' => 'jabatan'], function() {
            Route::get('/', 'JabatanController@index')->name('jabatan.index');
            Route::post('/store', 'JabatanController@store')->name('jabatan.store');
            Route::get('/{jabatan}/show', 'JabatanController@show')->name('jabatan.show');
            Route::get('/{jabatan}/edit', 'JabatanController@edit')->name('jabatan.edit');
            Route::patch('/{jabatan}/update', 'JabatanController@update')->name('jabatan.update');
            Route::get('/{jabatan}/delete', 'JabatanController@destroy')->name('jabatan.destroy');
        });

        Route::group(['prefix' => 'kembali_sewa'], function() {
            Route::get('/', 'KembaliSewaController@index')->name('kembali_sewa.index');
            Route::get('/create', 'KembaliSewaController@create')->name('kembali_sewa.create');
            Route::post('/store', 'KembaliSewaController@store')->name('kembali_sewa.store');
            Route::get('/{kembali_sewa}/show', 'KembaliSewaController@show')->name('kembali_sewa.show');
            Route::get('/{kembali_sewa}/edit', 'KembaliSewaController@edit')->name('kembali_sewa.edit');
            Route::patch('/{kembali_sewa}/update', 'KembaliSewaController@update')->name('kembali_sewa.update');
            Route::get('/{kembali_sewa}/delete', 'KembaliSewaController@destroy')->name('kembali_sewa.destroy');
        });

        Route::group(['prefix' => 'invoice_sewa'], function() {
            Route::get('/', 'InvoiceSewaController@index')->name('invoice_sewa.index');
            Route::get('/create', 'InvoiceSewaController@create')->name('invoice_sewa.create');
            Route::post('/store', 'InvoiceSewaController@store')->name('invoice_sewa.store');
            Route::get('/{invoice_sewa}/show', 'InvoiceSewaController@show')->name('invoice_sewa.show');
            Route::get('/{invoice_sewa}/edit', 'InvoiceSewaController@edit')->name('invoice_sewa.edit');
            Route::patch('/{invoice_sewa}/update', 'InvoiceSewaController@update')->name('invoice_sewa.update');
            Route::get('/{invoice_sewa}/delete', 'InvoiceSewaController@destroy')->name('invoice_sewa.destroy');
        });

        Route::group(['prefix' => 'pembayaran_sewa'], function() {
            Route::get('/', 'PembayaranController@index_sewa')->name('pembayaran_sewa.index');
            Route::get('/create', 'PembayaranController@create_sewa')->name('pembayaran_sewa.create');
            Route::post('/store', 'PembayaranController@store_sewa')->name('pembayaran_sewa.store');
            Route::get('/{pembayaran_sewa}/show', 'PembayaranController@show_sewa')->name('pembayaran_sewa.show');
            Route::get('/{pembayaran_sewa}/edit', 'PembayaranController@edit_sewa')->name('pembayaran_sewa.edit');
            Route::patch('/{pembayaran_sewa}/update', 'PembayaranController@update_sewa')->name('pembayaran_sewa.update');
            Route::get('/{pembayaran_sewa}/delete', 'PembayaranController@destroy_sewa')->name('pembayaran_sewa.destroy');
        });
        Route::group(['prefix' => 'inven_outlet'], function() {
            Route::get('/', 'InventoryOutletController@index')->name('inven_outlet.index');
            Route::get('/create', 'InventoryOutletController@create')->name('inven_outlet.create');
            Route::post('/store', 'InventoryOutletController@store')->name('inven_outlet.store');
            Route::get('/{inven_outlet}/show', 'InventoryOutletController@show')->name('inven_outlet.show');
            Route::get('/{inven_outlet}/edit', 'InventoryOutletController@edit')->name('inven_outlet.edit');
            Route::patch('/{inven_outlet}/update', 'InventoryOutletController@update')->name('inven_outlet.update');
            Route::get('/{inven_outlet}/delete', 'InventoryOutletController@destroy')->name('inven_outlet.destroy');
        });

        Route::group(['prefix' => 'mutasiGO'], function() {
            Route::get('/', 'MutasiController@index_outlet')->name('mutasigalery.index');
            Route::get('/create', 'MutasiController@create_outlet')->name('mutasigalery.create');
            Route::post('/store', 'MutasiController@store_outlet')->name('mutasigalery.store');
            Route::get('/{mutasiGO}/show', 'MutasiController@show_outlet')->name('mutasigalery.show');
            Route::get('/{mutasiGO}/acc', 'MutasiController@acc_outlet')->name('mutasigalery.acc');
            Route::get('/{mutasiGO}/edit', 'MutasiController@edit_outlet')->name('mutasigalery.edit');
            Route::patch('/{mutasiGO}/update', 'MutasiController@update_outlet')->name('mutasigalery.update');
            Route::get('/{mutasiGO}/delete', 'MutasiController@destroy_outlet')->name('mutasigalery.destroy');
        });

        Route::group(['prefix' => 'mutasiOG'], function() {
            Route::get('/', 'MutasiController@index_outletgalery')->name('mutasioutlet.index');
            Route::get('{returpenjualan}/create', 'MutasiController@create_outletgalery')->name('mutasioutlet.create');
            Route::post('/store', 'MutasiController@store_outletgalery')->name('mutasioutlet.store');
            Route::get('/{mutasiOG}/show', 'MutasiController@show_outletgalery')->name('mutasioutlet.show');
            Route::get('/{mutasiOG}/edit', 'MutasiController@edit_outletgalery')->name('mutasioutlet.edit');
            Route::patch('/{mutasiOG}/update', 'MutasiController@update_outletgalery')->name('mutasioutlet.update');
            Route::get('/{mutasiOG}/delete', 'MutasiController@destroy_outletgalery')->name('mutasioutlet.destroy');
        });

        Route::group(['prefix' => 'kas_pusat'], function() {
            Route::get('/', 'KasController@index_pusat')->name('kas_pusat.index');
            Route::get('/create', 'KasController@create_pusat')->name('kas_pusat.create');
            Route::post('/store', 'KasController@store_pusat')->name('kas_pusat.store');
            Route::get('/{kas_pusat}/show', 'KasController@show_pusat')->name('kas_pusat.show');
            Route::get('/{kas_pusat}/edit', 'KasController@edit_pusat')->name('kas_pusat.edit');
            Route::patch('/{kas_pusat}/update', 'KasController@update_pusat')->name('kas_pusat.update');
            Route::get('/{kas_pusat}/delete', 'KasController@destroy_pusat')->name('kas_pusat.destroy');
        });

        Route::group(['prefix' => 'kas_gallery'], function() {
            Route::get('/', 'KasController@index_gallery')->name('kas_gallery.index');
            Route::get('/create', 'KasController@create_gallery')->name('kas_gallery.create');
            Route::post('/store', 'KasController@store_gallery')->name('kas_gallery.store');
            Route::get('/{kas_gallery}/show', 'KasController@show_gallery')->name('kas_gallery.show');
            Route::get('/{kas_gallery}/edit', 'KasController@edit_gallery')->name('kas_gallery.edit');
            Route::patch('/{kas_gallery}/update', 'KasController@update_gallery')->name('kas_gallery.update');
            Route::get('/{kas_gallery}/delete', 'KasController@destroy_gallery')->name('kas_gallery.destroy');
        });
        
        Route::group(['prefix' => 'mutasiGG'], function() {
            Route::get('/', 'MutasiController@index_ghgalery')->name('mutasighgalery.index');
            Route::get('/create', 'MutasiController@create_ghgalery')->name('mutasighgalery.create');
            Route::post('/store', 'MutasiController@store_ghgalery')->name('mutasighgalery.store');
            Route::get('/{mutasiGG}/show', 'MutasiController@show_ghgalery')->name('mutasighgalery.show');
            Route::get('/{mutasiGG}/edit', 'MutasiController@edit_ghgalery')->name('mutasighgalery.edit');
            Route::patch('/{mutasiGG}/update', 'MutasiController@update_ghgalery')->name('mutasighgalery.update');
            Route::get('/{mutasiGG}/delete', 'MutasiController@destroy_ghgalery')->name('mutasighgalery.destroy');
        });

        Route::group(['prefix' => 'inven_greenhouse'], function() {
            Route::get('/', 'InventoryGreenhouseController@index')->name('inven_greenhouse.index');
            Route::get('/create', 'InventoryGreenhouseController@create')->name('inven_greenhouse.create');
            Route::post('/store', 'InventoryGreenhouseController@store')->name('inven_greenhouse.store');
            Route::get('/{inven_greenhouse}/show', 'InventoryGreenhouseController@show')->name('inven_greenhouse.show');
            Route::get('/{inven_greenhouse}/edit', 'InventoryGreenhouseController@edit')->name('inven_greenhouse.edit');
            Route::patch('/{inven_greenhouse}/update', 'InventoryGreenhouseController@update')->name('inven_greenhouse.update');
            Route::get('/{inven_greenhouse}/delete', 'InventoryGreenhouseController@destroy')->name('inven_greenhouse.destroy');
        });

        Route::group(['prefix' => 'mutasiGAG'], function() {
            Route::get('/', 'MutasiController@index_galerygalery')->name('mutasigalerygalery.index');
            Route::get('{returpenjualan}/create', 'MutasiController@create_galerygalery')->name('mutasigalerygalery.create');
            Route::post('/store', 'MutasiController@store_galerygalery')->name('mutasigalerygalery.store');
            Route::get('/{mutasiGAG}/show', 'MutasiController@show_galerygalery')->name('mutasigalerygalery.show');
            Route::get('/{mutasiGAG}/edit', 'MutasiController@edit_galerygalery')->name('mutasigalerygalery.edit');
            Route::patch('/{mutasiGAG}/update', 'MutasiController@update_galerygalery')->name('mutasigalerygalery.update');
            Route::get('/{mutasiGAG}/delete', 'MutasiController@destroy_galerygalery')->name('mutasigalerygalery.destroy');
        });

        Route::group(['prefix' => 'kas_pusat'], function() {
            Route::get('/', 'TransaksiKasController@index_pusat')->name('kas_pusat.index');
            Route::get('/create', 'TransaksiKasController@create_pusat')->name('kas_pusat.create');
            Route::post('/store', 'TransaksiKasController@store_pusat')->name('kas_pusat.store');
            Route::get('/{kas_pusat}/show', 'TransaksiKasController@show_pusat')->name('kas_pusat.show');
            Route::get('/{kas_pusat}/edit', 'TransaksiKasController@edit_pusat')->name('kas_pusat.edit');
            Route::patch('/{kas_pusat}/update', 'TransaksiKasController@update_pusat')->name('kas_pusat.update');
            Route::get('/{kas_pusat}/delete', 'TransaksiKasController@destroy_pusat')->name('kas_pusat.destroy');
        });

        Route::get('posts/{post}/log', 'PostController@log')->name('posts.log');
        Route::resource('posts', 'PostController');
    });
});