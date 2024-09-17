<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Models\Pembelian;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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
         * 
         */
        Route::get('/get-bulan-inden/{supplier_id}', 'MutasiindensController@getBulanInden')->name('getBulan'); //pur
        Route::get('/get-kode-inden/{bulan_inden}/{supplier_id}', 'MutasiindensController@getkodeInden')->name('getKode'); //pur
        Route::get('/get-kategori-inden/{kode_inden}/{bulan_inden}/{supplier_id}', 'MutasiindensController@getkategoriInden')->name('getKategori'); //pur
        Route::get('/get-kategori-inden-edit/{kode_inden}/{bulan_inden}/{supplier_id}', 'MutasiindensController@getkategoriIndenEdit')->name('getKategoriEdit'); //pur

        Route::get('/logout', 'LogoutController@perform')->name('logout.perform'); //pur
        Route::get('checkPromo', 'PromoController@checkPromo')->name('checkPromo');
        Route::get('getPromo', 'PromoController@getPromo')->name('getPromo');
        Route::get('getProdukTerjual', 'ProdukTerjualController@getProdukTerjual')->name('getProdukTerjual');
        Route::post('addKomponen', 'KomponenProdukTerjualController@addKomponen')->name('addKomponen');
        Route::get('getProdukDo', 'DeliveryOrderController@getProdukDo')->name('getProdukDo');
        Route::get('rekeningPerLokasi', 'TransaksiKasController@rekeningPerLokasi')->name('rekeningPerLokasi');
        Route::get('log/{id}', 'TransaksiKasController@log')->name('kas.log');


        Route::group(['prefix' => 'dashboard'], function() {
            Route::get('/', 'DashboardController@index')->name('dashboard.index'); //pur
            Route::post('/postauditor', 'DashboardController@update_auditor')->name('auditor.update');
            Route::post('/bukakuncistore', 'DashboardController@bukakunci')->name('bukakunci.store');
            Route::get('/top-products', 'DashboardController@getTopProducts')->name('getTopProduk');
            Route::get('/top_minus_produk', 'DashboardController@getTopMinusProduk')->name('getTopMinusProduk');
            Route::get('/top_sales', 'DashboardController@getTopSales')->name('getTopSales');
            Route::get('/loyalty', 'DashboardController@getLoyalty')->name('getLoyalty');
            Route::get('/uang_keluar', 'DashboardController@uang_keluar')->name('uang_keluar');
            Route::get('/tagihan_supplier', 'DashboardController@tagihan_supplier')->name('tagihan_supplier');
        });

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
            Route::get('/editProfile', 'UsersController@edit_profile')->name('profile.edit');
            Route::patch('/updateProfile', 'UsersController@update_profile')->name('profile.update');
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
            Route::get('/{produk}/delete', 'ProdukController@destroy')->name('produks.destroy');
            Route::get('/pdf', 'ProdukController@pdf')->name('produks.pdf');
            Route::get('/excel', 'ProdukController@excel')->name('produks.excel');
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
            Route::get('{penjualan}/pdfinvoicepenjualan', 'PenjualanController@pdfinvoicepenjualan')->name('pdfinvoicepenjualan.generate');
            Route::get('/{penjualan}/audit', 'PenjualanController@audit')->name('auditpenjualan.edit');
            Route::patch('/storeaudit', 'PenjualanController@audit_update')->name('auditpenjualan.update');
            Route::get('/{penjualan}/showaudit', 'PenjualanController@audit_show')->name('auditpenjualan.show');
            Route::get('/{penjualan}/view', 'PenjualanController@view_penjualan')->name('penjualan.view');
        });

        Route::group(['prefix' => 'purchase'], function() {
            //akses purchase
            Route::get('/pembelian', 'PembelianController@index')->name('pembelian.index'); //pur,adm, fin, aud
            Route::get('/pembelian/create', 'PembelianController@create')->name('pembelian.create'); //pur
            Route::post('/store_po', 'PembelianController@store_po')->name('pembelianpo.store'); //pur
            Route::get('/pembelian/{datapo}/editpurchase_po', 'PembelianController@po_editpurchase')->name('pembelian.editpurchase'); //pur
            Route::post('/{datapo}/update_po_purchase', 'PembelianController@po_update_purchase')->name('pembelian.updatepurchase'); //pur
            Route::get('/pembelian/{datapo}/show', 'PembelianController@show')->name('pembelian.show');  //pur,adm, fin, aud
            Route::patch('/{datapo}/update', 'PembelianController@gambarpo_update')->name('gambarpo.update');  //pur,adm, fin, aud

            //akses admin
            Route::get('/pembelian/{datapo}/edit_po', 'PembelianController@po_edit')->name('pembelian.edit'); //adm
            Route::post('/{datapo}/update_po', 'PembelianController@po_update')->name('pembelian.update'); //adm

            //audit
            Route::get('/pembelian/{datapo}/edit_po_audit', 'PembelianController@po_edit_audit')->name('pembelian.editaudit'); //aud
            Route::post('/{datapo}/update_po_audit', 'PembelianController@po_update_audit')->name('pembelian.updateaudit'); //aud


            //PURCHASE
            Route::get('/invoice', 'PembelianController@invoice')->name('invoicebeli.index'); //pur, fin
            Route::get('/invoice/{type}/{datapo}/createinv', 'PembelianController@createinvoice')->name('invoicebiasa.create'); //pur
            Route::post('/store_inv', 'PembelianController@storeinvoice')->name('invoicepo.store'); //pur
            //purchase dan finance halaman untuk edit invoice
            Route::get('/invoice/{datapo}/edit_inv_nominal', 'PembelianController@edit_invoice_purchase')->name('invoicepurchase.edit'); //pur, fin
            Route::put('/update/{idinv}/nominal', 'PembelianController@update_purchase_invoice')->name('invoice_purchase.update'); //pur, fin

 
            //inden
            Route::get('/invoice/{datapo}/editinvoice', 'PembelianController@editinvoice')->name('editinvoice.edit'); //pur, fin
            Route::patch('/{datapo}/editinvoiceupdate', 'PembelianController@editinvoiceupdate')->name('editinvoice.update'); //pur, fin
            //pembelian
            Route::get('/invoice/{datapo}/edit', 'PembelianController@edit_invoice')->name('invoice.edit'); //fin
            Route::put('/update/{idinv}', 'PembelianController@update_invoice')->name('invoice.update'); //fin
            Route::get('/invoice/{datapo}/show', 'PembelianController@show_invoice')->name('invoice.show'); //pur, fin

            Route::get('/pembelian/createinden', 'PembelianController@createinden')->name('pembelianinden.create'); //pur
            Route::post('/storeinden', 'PembelianController@store_inden')->name('inden.store'); //pur
            // Route::get('/createinvinden', 'PembelianController@createinvoiceinden')->name('invoiceinden.create');

            Route::get('/retur', 'PembelianController@index_retur')->name('returbeli.index'); //pur, fin
            Route::get('/retur/create', 'PembelianController@create_retur')->name('returbeli.create'); //pur
            Route::post('/retur/store', 'PembelianController@store_retur')->name('returbeli.store'); //pur

            Route::put('/retur/{idretur}/update', 'PembelianController@update_retur_finance')->name('returfinance.update'); //fin
            Route::patch('/returbeli/{retur_id}/update', 'PembelianController@update_retur_purchase')->name('retur_purchase.update'); //pur
            
            Route::get('/pembayaran', 'PembayaranController@index_po')->name('pembayaranbeli.index'); //pur, fin
            Route::post('/pembayaran/store', 'PembayaranController@store_po')->name('pembayaranbeli.store'); //fin
            Route::post('/refund/store', 'PembayaranController@bayar_refund')->name('bayarrefund.store'); //fin
            Route::post('/refundinden/store', 'PembayaranController@refundInden')->name('refundinden.store'); //fin
            Route::get('/pembayaran/{id}/edit', 'PembayaranController@edit_pembelian')->name('pembayaran_pembelian.edit'); //fin
            Route::patch('/pembayaran/{id}/update', 'PembayaranController@update_pembelian')->name('pembayaran_pembelian.update'); //fin

             Route::get('/returbeli/{retur_id}/show', 'PembelianController@show_returpo')->name('returbeli.show');  //fin , pur
             Route::get('/returbeli/{retur_id}/edit', 'PembelianController@edit_returpo')->name('returbeli.edit'); //pur, fin

        });

        Route::group(['prefix' => 'kontrak'], function() {
            Route::get('/', 'KontrakController@index')->name('kontrak.index');
            Route::get('/create', 'KontrakController@create')->name('kontrak.create');
            Route::post('/store', 'KontrakController@store')->name('kontrak.store');
            Route::get('/{kontrak}/show', 'KontrakController@show')->name('kontrak.show');
            Route::get('/{kontrak}/edit', 'KontrakController@edit')->name('kontrak.edit');
            Route::patch('/{kontrak}/update', 'KontrakController@update')->name('kontrak.update');
            Route::get('/{kontrak}/delete', 'KontrakController@destroy')->name('kontrak.destroy');
            Route::get('/{kontrak}/pdfKontrak', 'KontrakController@pdfKontrak')->name('kontrak.pdfKontrak');
            Route::get('/{kontrak}/excelPergantian', 'KontrakController@excelPergantian')->name('kontrak.excelPergantian');
            Route::get('/{id}/selesai', 'KontrakController@selesai')->name('kontrak.selesai');
        });

        Route::group(['prefix' => 'dopenjualan'], function() {
            Route::get('/', 'DopenjualanController@index')->name('dopenjualan.index');
            Route::get('{penjualan}/create', 'DopenjualanController@create')->name('dopenjualan.create');
            Route::post('/store', 'DopenjualanController@store')->name('dopenjualan.store');
            Route::get('/{dopenjualan}/show', 'DopenjualanController@show')->name('dopenjualan.show');
            Route::get('/{dopenjualan}/edit', 'DopenjualanController@edit')->name('dopenjualan.edit');
            Route::patch('/{dopenjualan}/update', 'DopenjualanController@update')->name('dopenjualan.update');
            Route::get('/{dopenjualan}/delete', 'DopenjualanController@destroy')->name('dopenjualan.destroy');
            Route::get('{dopenjualan}/pdfdopenjualan', 'DopenjualanController@pdfdopenjualan')->name('pdfdopenjualan.generate');
            Route::get('{dopenjualan}/auditdopenjualan', 'DopenjualanController@audit')->name('auditdopenjualan.edit');
            Route::patch('/auditdopenjualan', 'DopenjualanController@audit_update')->name('auditdopenjualan.update');
        });

        Route::group(['prefix' => 'pembayaran'], function() {
            Route::get('/', 'PembayaranController@index')->name('pembayaran.index');
            Route::get('/create', 'PembayaranController@create')->name('pembayaran.create');
            Route::post('/store', 'PembayaranController@store')->name('pembayaran.store');
            Route::get('/{pembayaran}/show', 'PembayaranController@show')->name('pembayaran.show');
            Route::get('/{pembayaran}/edit', 'PembayaranController@edit')->name('pembayaran.edit');
            Route::patch('/{pembayaran}/update', 'PembayaranController@update')->name('pembayaran.update');
            Route::get('/{pembayaran}/delete', 'PembayaranController@destroy')->name('pembayaran.destroy');
            Route::post('/store_invpo', 'PembayaranController@store_bayar_po')->name('bayarpo.store'); //fin
            Route::post('/store_mutasiinden', 'PembayaranController@store_bayar_mutasi')->name('pembayaranmutasi.store'); //fin
        });

        Route::group(['prefix' => 'form'], function() {
            Route::get('/', 'FormPerangkaiController@index')->name('form.index');
            Route::get('/create', 'FormPerangkaiController@create')->name('form.create');
            Route::post('/store', 'FormPerangkaiController@store')->name('form.store');
            Route::get('/{form}/show', 'FormPerangkaiController@show')->name('form.show');
            Route::get('/{form}/edit', 'FormPerangkaiController@edit')->name('form.edit');
            Route::patch('/{form}/update', 'FormPerangkaiController@update')->name('form.update');
            Route::get('/{form}/delete', 'FormPerangkaiController@destroy')->name('form.destroy');
            Route::get('/{form}/cetak', 'FormPerangkaiController@cetak')->name('form.cetak');
        });

        Route::group(['prefix' => 'formpenjualan'], function() {
            Route::get('/', 'FormPerangkaiController@penjualan_index')->name('formpenjualan.index');
            Route::get('/create', 'FormPerangkaiController@penjualan_create')->name('formpenjualan.create');
            Route::post('/store', 'FormPerangkaiController@penjualan_store')->name('formpenjualan.store');
            Route::post('/storemutasi', 'FormPerangkaiController@mutasi_store')->name('formmutasi.store');
            Route::get('/{formpenjualan}/show', 'FormPerangkaiController@penjualan_show')->name('formpenjualan.show');
            Route::get('/{formpenjualan}/edit', 'FormPerangkaiController@penjualan_edit')->name('formpenjualan.edit');
            Route::patch('/{formpenjualan}/update', 'FormPerangkaiController@penjualan_update')->name('formpenjualan.update');
            Route::get('/{formpenjualan}/delete', 'FormPerangkaiController@penjualan_destroy')->name('formpenjualan.destroy');
            Route::get('/{formpenjualan}/cetakpenjualan', 'FormPerangkaiController@cetak_penjualan')->name('formpenjualan.cetak');
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
            Route::get('/{returpenjualan}/payment', 'ReturpenjualanController@payment')->name('returpenjualan.payment');
            Route::post('/paymentretur', 'ReturpenjualanController@paymentretur')->name('returpenjualan.paymentretur');
            Route::get('/{returpenjualan}/auditretur', 'ReturpenjualanController@audit')->name('auditretur.edit');
            Route::patch('/storeauditretur', 'ReturpenjualanController@auditretur_update')->name('auditretur.update');
            Route::get('/{returpenjualan}/view', 'ReturpenjualanController@view_retur')->name('returpenjualan.view');
        });

        Route::group(['prefix' => 'inven_galeri'], function() {
            Route::get('/', 'InventoryGalleryController@index')->name('inven_galeri.index'); //all
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
            Route::get('/{invoice_sewa}/cetak', 'InvoiceSewaController@cetak')->name('invoice_sewa.cetak');
        });

        Route::group(['prefix' => 'pembayaran_sewa'], function() {
            Route::get('/', 'PembayaranController@index_sewa')->name('pembayaran_sewa.index');
            Route::post('/store', 'PembayaranController@store_sewa')->name('pembayaran_sewa.store');
            Route::patch('/{pembayaran_sewa}/update', 'PembayaranController@update_sewa')->name('pembayaran_sewa.update');
            Route::get('/{pembayaran_sewa}/show', 'PembayaranController@show_sewa')->name('pembayaran_sewa.show');
            Route::get('/{pembayaran_sewa}/edit', 'PembayaranController@edit_sewa')->name('pembayaran_sewa.edit');
            Route::get('/create', 'PembayaranController@create_sewa')->name('pembayaran_sewa.create');
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
            Route::get('/', 'MutasiController@index_outlet')->name('mutasigalery.index'); //admin
            Route::get('/create', 'MutasiController@create_outlet')->name('mutasigalery.create');
            Route::post('/store', 'MutasiController@store_outlet')->name('mutasigalery.store');
            Route::get('/{mutasiGO}/show', 'MutasiController@show_outlet')->name('mutasigalery.show');
            Route::get('/{mutasiGO}/acc', 'MutasiController@acc_outlet')->name('mutasigalery.acc');
            Route::get('/{mutasiGO}/edit', 'MutasiController@edit_outlet')->name('mutasigalery.edit');
            Route::patch('/{mutasiGO}/update', 'MutasiController@update_outlet')->name('mutasigalery.update');
            Route::get('/{mutasiGO}/delete', 'MutasiController@destroy_outlet')->name('mutasigalery.destroy');
            Route::get('/{mutasiGO}/payment', 'MutasiController@payment_outlet')->name('mutasigalery.payment');
            Route::post('/paymentmutasi', 'MutasiController@paymentmutasi')->name('mutasi.paymentmutasi');
            Route::get('/{mutasiGO}/view', 'MutasiController@view_outlet')->name('mutasigalery.view');
            Route::get('/{mutasiGO}/auditGO', 'MutasiController@audit_GO')->name('auditmutasigalery.edit');
            Route::patch('/auditmutasiGO', 'MutasiController@audit_GOUpdate')->name('auditmutasigalery.update');
            Route::get('/{mutasiGO}/cetakmutasigalery', 'FormPerangkaiController@cetak_mutasigalery')->name('formmutasigalery.cetak');
        });

        Route::group(['prefix' => 'mutasiOG'], function() { //adm
            Route::get('/', 'MutasiController@index_outletgalery')->name('mutasioutlet.index');
            Route::get('{returpenjualan}/create', 'MutasiController@create_outletgalery')->name('mutasioutlet.create');
            Route::post('/store', 'MutasiController@store_outletgalery')->name('mutasioutlet.store');
            Route::get('/{mutasiOG}/show', 'MutasiController@show_outletgalery')->name('mutasioutlet.show');
            Route::get('/{mutasiOG}/edit', 'MutasiController@edit_outletgalery')->name('mutasioutlet.edit');
            Route::patch('/{mutasiOG}/update', 'MutasiController@update_outletgalery')->name('mutasioutlet.update');
            Route::get('/{mutasiOG}/delete', 'MutasiController@destroy_outletgalery')->name('mutasioutlet.destroy');
            Route::get('/{mutasiOG}/payment', 'MutasiController@payment_outletgalery')->name('mutasioutlet.payment');
            Route::get('/{mutasiOG}/view', 'MutasiController@view_outletgalery')->name('mutasioutlet.view');
            Route::get('/{mutasiOG}/auditOG', 'MutasiController@audit_OG')->name('auditmutasioutlet.edit');
            Route::patch('/auditmutasiOG', 'MutasiController@audit_OGUpdate')->name('auditmutasioutlet.update');
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

        Route::group(['prefix' => 'kas_gallery'], function() {
            Route::get('/', 'TransaksiKasController@index_gallery')->name('kas_gallery.index');
            Route::get('/create', 'TransaksiKasController@create_gallery')->name('kas_gallery.create');
            Route::post('/store', 'TransaksiKasController@store_gallery')->name('kas_gallery.store');
            Route::get('/{kas_gallery}/show', 'TransaksiKasController@show_gallery')->name('kas_gallery.show');
            Route::get('/{kas_gallery}/edit', 'TransaksiKasController@edit_gallery')->name('kas_gallery.edit');
            Route::patch('/{kas_gallery}/update', 'TransaksiKasController@update_gallery')->name('kas_gallery.update');
            Route::get('/{kas_gallery}/delete', 'TransaksiKasController@destroy_gallery')->name('kas_gallery.destroy');
        });
        
        Route::group(['prefix' => 'mutasiGG'], function() { //pur, fin, adm, aud
            Route::get('/', 'MutasiController@index_ghgalery')->name('mutasighgalery.index');
            Route::get('/create', 'MutasiController@create_ghgalery')->name('mutasighgalery.create');
            Route::post('/store', 'MutasiController@store_ghgalery')->name('mutasighgalery.store');
            Route::get('/{mutasiGG}/show', 'MutasiController@show_ghgalery')->name('mutasighgalery.show');
            Route::get('/{mutasiGG}/edit', 'MutasiController@edit_ghgalery')->name('mutasighgalery.edit');
            Route::patch('/{mutasiGG}/update', 'MutasiController@update_ghgalery')->name('mutasighgalery.update');
            Route::get('/{mutasiGG}/delete', 'MutasiController@destroy_ghgalery')->name('mutasighgalery.destroy');
            Route::get('/{mutasiGG}/payment', 'MutasiController@payment_ghgalery')->name('mutasighgalery.payment');
            Route::get('/{mutasiGG}/view', 'MutasiController@view_ghgalery')->name('mutasighgalery.view');
            Route::get('/{mutasiGG}/auditGG', 'MutasiController@audit_GG')->name('auditmutasighgalery.edit');
            Route::patch('/auditmutasiGG', 'MutasiController@audit_GGUpdate')->name('auditmutasighgalery.update');
            Route::get('/get-products-by-lokasi',  'MutasiController@getProductsByLokasi')->name('getProductsByLokasi');
        });

        Route::group(['prefix' => 'inven_greenhouse'], function() { //pur, fin, aud
            Route::get('/', 'InventoryGreenhouseController@index')->name('inven_greenhouse.index');
            Route::get('/create', 'InventoryGreenhouseController@create')->name('inven_greenhouse.create');
            Route::post('/store', 'InventoryGreenhouseController@store')->name('inven_greenhouse.store');
            Route::get('/{inven_greenhouse}/show', 'InventoryGreenhouseController@show')->name('inven_greenhouse.show');
            Route::get('/{inven_greenhouse}/edit', 'InventoryGreenhouseController@edit')->name('inven_greenhouse.edit');
            Route::patch('/{inven_greenhouse}/update', 'InventoryGreenhouseController@update')->name('inven_greenhouse.update');
            Route::get('/{inven_greenhouse}/delete', 'InventoryGreenhouseController@destroy')->name('inven_greenhouse.destroy');
        });

        //purchase
        
        //inden ke galery
        //inden ke greenhouse
        Route::group(['prefix' => 'mutasiIG'], function() {  //pur, fin, adm, aud
            Route::get('/', 'MutasiindensController@index_indengh')->name('mutasiindengh.index');
            Route::get('/create', 'MutasiindensController@create_indengh')->name('mutasiindengh.create'); //pur
            Route::get('/{mutasiIG}/edit', 'MutasiindensController@edit_indengh')->name('mutasiindengh.edit');  //fin
            Route::get('/{mutasiIG}/editpurchase', 'MutasiindensController@editpurchase_indengh')->name('mutasiindengh.editpurchase'); //pur
            Route::get('/{mutasiIG}/editfinance', 'MutasiindensController@editfinance_indengh')->name('mutasiindengh.editfinance'); //fin
            Route::get('/{mutasiIG}/show', 'MutasiindensController@show_indengh')->name('mutasiindengh.show'); //all
            Route::post('/store/retur', 'MutasiindensController@store_retur')->name('retur.store'); //pur
            Route::post('/store', 'MutasiindensController@store_indengh')->name('mutasiindengh.store'); //pur
            Route::patch('/{mutasiIG}/update', 'MutasiindensController@update_indengh')->name('mutasiindengh.update'); //pur, fin
            Route::patch('/mutasiindengh/{mutasiIG}/update-pembuku', 'MutasiindensController@updatePembuku')->name('mutasiindengh.updatePembuku'); //fin

            // Route::patch('/{mutasiIG}/update_gambar', 'MutasiindensController@updategambar_indengh')->name('gambarinden.update');
            Route::get('/{mutasiIG}/delete', 'MutasiindensController@destroy_indengh')->name('mutasiindengh.destroy');
            
        });
        
        Route::group(['prefix' => 'returinden'], function() {  //fin, pur
            Route::patch('/{idretur}/update-pembuku', 'MutasiindensController@updatePembukuRetur')->name('returinden.updatePembuku'); //fin
            Route::get('/{mutasiIG}/create/retur', 'MutasiindensController@create_retur')->name('create.retur'); //pur
            Route::get('/{idretur}/retur/edit', 'MutasiindensController@edit_retur')->name('edit.retur'); //pur, fin
            Route::patch('/{idretur}/update', 'MutasiindensController@update_retur')->name('returinden.update'); //pur,fin
            Route::get('/', 'MutasiindensController@index_returinden')->name('returinden.index'); //all
            Route::get('/{mutasiIG}/show', 'MutasiindensController@show_returinden')->name('show.returinden'); //all


        });

        Route::group(['prefix' => 'inven_inden'], function() { //pur, fin, aud
            Route::get('/', 'InventoryIndenController@index')->name('inven_inden.index');
            Route::get('/create', 'InventoryIndenController@create')->name('inven_inden.create');
            Route::post('/store', 'InventoryIndenController@store')->name('inven_inden.store');
            Route::get('/{inven_inden}/show', 'InventoryIndenController@show')->name('inven_inden.show');
            Route::get('/{inven_inden}/edit', 'InventoryIndenController@edit')->name('inven_inden.edit');
            Route::patch('/{inven_inden}/update', 'InventoryIndenController@update')->name('inven_inden.update');
            Route::get('/{inven_inden}/delete', 'InventoryIndenController@destroy')->name('inven_inden.destroy');
        });

        Route::group(['prefix' => 'inven_gudang'], function() { //pur, fin, aud
            Route::get('/', 'InventoryGudangController@index')->name('inven_gudang.index');
            Route::get('/create', 'InventoryGudangController@create')->name('inven_gudang.create');
            Route::post('/store', 'InventoryGudangController@store')->name('inven_gudang.store');
            Route::get('/{inven_gudang}/show', 'InventoryGudangController@show')->name('inven_gudang.show');
            Route::get('/{inven_gudang}/edit', 'InventoryGudangController@edit')->name('inven_gudang.edit');
            Route::patch('/{inven_gudang}/update', 'InventoryGudangController@update')->name('inven_gudang.update');
            Route::get('/{inven_gudang}/delete', 'InventoryGudangController@destroy')->name('inven_gudang.destroy');
        });


        //endpurchase


        Route::group(['prefix' => 'mutasiGAG'], function() { //pur, aud, adm, fin
            Route::get('/', 'MutasiController@index_galerygalery')->name('mutasigalerygalery.index');
            Route::get('/create', 'MutasiController@create_galerygalery')->name('mutasigalerygalery.create'); 
            Route::post('/store', 'MutasiController@store_galerygalery')->name('mutasigalerygalery.store');
            Route::get('/{mutasiGAG}/show', 'MutasiController@show_galerygalery')->name('mutasigalerygalery.show');
            Route::get('/{mutasiGAG}/edit', 'MutasiController@edit_galerygalery')->name('mutasigalerygalery.edit');
            Route::patch('/{mutasiGAG}/update', 'MutasiController@update_galerygalery')->name('mutasigalerygalery.update');
            Route::get('/{mutasiGAG}/delete', 'MutasiController@destroy_galerygalery')->name('mutasigalerygalery.destroy');
            Route::get('/{mutasiGAG}/payment', 'MutasiController@payment_galerygalery')->name('mutasigalerygalery.payment');
            Route::get('/{mutasiGAG}/view', 'MutasiController@view_galerygalery')->name('mutasigalerygalery.view');
            Route::get('/{mutasiGAG}/auditGAG', 'MutasiController@audit_GAG')->name('auditmutasigalerygalery.edit');
            Route::patch('/auditmutasiGAG', 'MutasiController@audit_GAGUpdate')->name('auditmutasigalerygalery.update');
        });

        Route::group(['prefix' => 'pemakaian_sendiri'], function() {
            Route::get('/', 'PemakaianSendiriController@index')->name('pemakaian_sendiri.index');
            Route::get('/create', 'PemakaianSendiriController@create')->name('pemakaian_sendiri.create');
            Route::post('/store', 'PemakaianSendiriController@store')->name('pemakaian_sendiri.store');
            Route::get('/{pemakaian_sendiri}/show', 'PemakaianSendiriController@show')->name('pemakaian_sendiri.show');
            Route::get('/{pemakaian_sendiri}/edit', 'PemakaianSendiriController@edit')->name('pemakaian_sendiri.edit');
            Route::patch('/{pemakaian_sendiri}/update', 'PemakaianSendiriController@update')->name('pemakaian_sendiri.update');
            Route::get('/{pemakaian_sendiri}/delete', 'PemakaianSendiriController@destroy')->name('pemakaian_sendiri.destroy');
        });

        Route::group(['prefix' => 'laporan'], function() {
            Route::get('/kontrak', 'LaporanController@kontrak_index')->name('laporan.kontrak');
            Route::get('/kontrak-pdf', 'LaporanController@kontrak_pdf')->name('laporan.kontrak-pdf');
            Route::get('/kontrak-excel', 'LaporanController@kontrak_excel')->name('laporan.kontrak-excel');
            Route::get('/tagihan_sewa', 'LaporanController@tagihan_sewa_index')->name('laporan.tagihan_sewa');
            Route::get('/tagihan_sewa-pdf', 'LaporanController@tagihan_sewa_pdf')->name('laporan.tagihan_sewa-pdf');
            Route::get('/tagihan_sewa-excel', 'LaporanController@tagihan_sewa_excel')->name('laporan.tagihan_sewa-excel');
            Route::get('/do_sewa', 'LaporanController@do_sewa_index')->name('laporan.do_sewa');
            Route::get('/do_sewa-pdf', 'LaporanController@do_sewa_pdf')->name('laporan.do_sewa-pdf');
            Route::get('/do_sewa-excel', 'LaporanController@do_sewa_excel')->name('laporan.do_sewa-excel');
            Route::get('/pergantian_sewa', 'LaporanController@pergantian_sewa_index')->name('laporan.pergantian_sewa');
            Route::get('/pergantian_sewa-pdf', 'LaporanController@pergantian_sewa_pdf')->name('laporan.pergantian_sewa-pdf');
            Route::get('/pergantian_sewa-excel', 'LaporanController@pergantian_sewa_excel')->name('laporan.pergantian_sewa-excel');
            Route::get('/penjualanproduk', 'LaporanController@penjualanproduk_index')->name('laporan.penjualanproduk');
            Route::get('/penjualanproduk-pdf', 'LaporanController@penjualanproduk_pdf')->name('laporan.penjualanproduk-pdf');
            Route::get('/penjualanproduk-excel', 'LaporanController@penjualanproduk_excel')->name('laporan.penjualanproduk-excel');
            Route::get('/pelanggan', 'LaporanController@pelanggan_index')->name('laporan.pelanggan');
            Route::get('/tagihanpelanggan-pdf', 'LaporanController@tagihanpelanggan_pdf')->name('laporan.tagihanpelanggan-pdf');
            Route::get('/tagihanpelanggan-excel', 'LaporanController@tagihanpelanggan_excel')->name('laporan.tagihanpelanggan-excel');
            Route::get('/pelanggan-pdf', 'LaporanController@pelanggan_pdf')->name('laporan.pelanggan-pdf');
            Route::get('/pelanggan-excel', 'LaporanController@pelanggan_excel')->name('laporan.pelanggan-excel');
            Route::get('/pembayaran', 'LaporanController@pembayaran_index')->name('laporan.pembayaran');
            Route::get('/pembayaran-pdf', 'LaporanController@pembayaran_pdf')->name('laporan.pembayaran-pdf');
            Route::get('/pembayaran-excel', 'LaporanController@pembayaran_excel')->name('laporan.pembayaran-excel');
            Route::get('/dopenjualan', 'LaporanController@dopenjualan_index')->name('laporan.dopenjualan');
            Route::get('/dopenjualan-pdf', 'LaporanController@dopenjualan_pdf')->name('laporan.dopenjualan-pdf');
            Route::get('/dopenjualan-excel', 'LaporanController@dopenjualan_excel')->name('laporan.dopenjualan-excel');
            Route::get('/returpenjualan', 'LaporanController@returpenjualan_index')->name('laporan.returpenjualan');
            Route::get('/returpenjualan-pdf', 'LaporanController@returpenjualan_pdf')->name('laporan.returpenjualan-pdf');
            Route::get('/returpenjualan-excel', 'LaporanController@returpenjualan_excel')->name('laporan.returpenjualan-excel');
            Route::get('/penjualan', 'LaporanController@penjualan_index')->name('laporan.penjualan');
            Route::get('/penjualan-pdf', 'LaporanController@penjualan_pdf')->name('laporan.penjualan-pdf');
            Route::get('/penjualan-excel', 'LaporanController@penjualan_excel')->name('laporan.penjualan-excel');
            Route::get('/mutasi', 'LaporanController@mutasi_index')->name('laporan.mutasi'); //pur
            Route::get('/mutasi-pdf', 'LaporanController@mutasi_pdf')->name('laporan.mutasi-pdf'); //pur
            Route::get('/mutasi-excel', 'LaporanController@mutasi_excel')->name('laporan.mutasi-excel'); //pur
            Route::get('/promo', 'LaporanController@promo_index')->name('laporan.promo');
            Route::get('/promo-pdf', 'LaporanController@promo_pdf')->name('laporan.promo-pdf');
            Route::get('/promo-excel', 'LaporanController@promo_excel')->name('laporan.promo-excel');
            Route::get('/mutasiinden', 'LaporanController@mutasiinden_index')->name('laporan.mutasiinden'); //pur, fin, aud
            Route::get('/mutasiinden-pdf', 'LaporanController@mutasiinden_pdf')->name('laporan.mutasiinden-pdf'); //pur
            Route::get('/mutasiinden-excel', 'LaporanController@mutasiinden_excel')->name('laporan.mutasiinden-excel'); //pur
            Route::get('/kas_pusat', 'LaporanController@kas_pusat_index')->name('laporan.kas_pusat');
            Route::get('/kas_pusat-pdf', 'LaporanController@kas_pusat_pdf')->name('laporan.kas_pusat-pdf');
            Route::get('/kas_pusat-excel', 'LaporanController@kas_pusat_excel')->name('laporan.kas_pusat-excel');
            Route::get('/kas_gallery', 'LaporanController@kas_gallery_index')->name('laporan.kas_gallery');
            Route::get('/kas_gallery-pdf', 'LaporanController@kas_gallery_pdf')->name('laporan.kas_gallery-pdf');
            Route::get('/kas_gallery-excel', 'LaporanController@kas_gallery_excel')->name('laporan.kas_gallery-excel');
            Route::get('/pembelian', 'LaporanController@pembelian_index')->name('laporan.pembelian'); //pur, fin, aud
            Route::get('/pembelian-pdf', 'LaporanController@pembelian_pdf')->name('laporan.pembelian-pdf'); //pur
            Route::get('/pembelian-excel', 'LaporanController@pembelian_excel')->name('laporan.pembelian-excel'); //pur
            Route::get('/pembelian_inden', 'LaporanController@pembelian_inden_index')->name('laporan.pembelian_inden'); //pur
            Route::get('/pembelian_inden-pdf', 'LaporanController@pembelian_inden_pdf')->name('laporan.pembelian_inden-pdf'); //pur
            Route::get('/pembelian_inden-excel', 'LaporanController@pembelian_inden_excel')->name('laporan.pembelian_inden-excel'); //pur
            Route::get('/stok_inden', 'LaporanController@stok_inden_index')->name('laporan.stok_inden'); //pur
            Route::get('/stok_inden-pdf', 'LaporanController@stok_inden_pdf')->name('laporan.stok_inden-pdf'); //pur
            Route::get('/stok_inden-excel', 'LaporanController@stok_inden_excel')->name('laporan.stok_inden-excel'); //pur
            Route::get('/hutang_supplier', 'LaporanController@hutang_supplier_index')->name('laporan.hutang_supplier'); //pur
            Route::get('/hutang_supplier-pdf', 'LaporanController@hutang_supplier_pdf')->name('laporan.hutang_supplier-pdf'); //pur
            Route::get('/hutang_supplier-excel', 'LaporanController@hutang_supplier_excel')->name('laporan.hutang_supplier-excel'); //pur
            Route::get('/retur_pembelian', 'LaporanController@retur_pembelian_index')->name('laporan.retur_pembelian'); //pur
            Route::get('/retur_pembelian-pdf', 'LaporanController@retur_pembelian_pdf')->name('laporan.retur_pembelian-pdf'); //pur
            Route::get('/retur_pembelian-excel', 'LaporanController@retur_pembelian_excel')->name('laporan.retur_pembelian-excel'); //pur
            Route::get('/retur_pembelian_inden', 'LaporanController@retur_pembelian_inden_index')->name('laporan.retur_pembelian_inden'); //pur
            Route::get('/retur_pembelian_inden-pdf', 'LaporanController@retur_pembelian_inden_pdf')->name('laporan.retur_pembelian_inden-pdf'); //pur
            Route::get('/retur_pembelian_inden-excel', 'LaporanController@retur_pembelian_inden_excel')->name('laporan.retur_pembelian_inden-excel'); //pur
            Route::get('/omset', 'LaporanController@omset_index')->name('laporan.omset');
            Route::get('/omset-pdf', 'LaporanController@omset_pdf')->name('laporan.omset-pdf');
            Route::get('/omset-excel', 'LaporanController@omset_excel')->name('laporan.omset-excel');
            Route::get('/promo', 'LaporanController@promo_index')->name('laporan.promo');
            Route::get('/promo-pdf', 'LaporanController@promo_pdf')->name('laporan.promo-pdf');
            Route::get('/promo-excel', 'LaporanController@promo_excel')->name('laporan.promo-excel');
            Route::get('/stok_gallery', 'LaporanController@stok_gallery_index')->name('laporan.stok_gallery');
            Route::get('/stok_gallery-pdf', 'LaporanController@stok_gallery_pdf')->name('laporan.stok_gallery-pdf');
            Route::get('/stok_gallery-excel', 'LaporanController@stok_gallery_excel')->name('laporan.stok_gallery-excel');
            Route::get('/stok_pusat', 'LaporanController@stok_pusat_index')->name('laporan.stok_pusat');
            Route::get('/stok_pusat-pdf', 'LaporanController@stok_pusat_pdf')->name('laporan.stok_pusat-pdf');
            Route::get('/stok_pusat-excel', 'LaporanController@stok_pusat_excel')->name('laporan.stok_pusat-excel');
            Route::get('/pemakaian_sendiri', 'LaporanController@pemakaian_sendiri_index')->name('laporan.pemakaian_sendiri');
            Route::get('/pemakaian_sendiri-pdf', 'LaporanController@pemakaian_sendiri_pdf')->name('laporan.pemakaian_sendiri-pdf');
            Route::get('/pemakaian_sendiri-excel', 'LaporanController@pemakaian_sendiri_excel')->name('laporan.pemakaian_sendiri-excel');
            Route::get('/bunga_keluar', 'LaporanController@bunga_keluar_index')->name('laporan.bunga_keluar');
            Route::get('/bunga_keluar-pdf', 'LaporanController@bunga_keluar_pdf')->name('laporan.bunga_keluar-pdf');
            Route::get('/bunga_keluar-excel', 'LaporanController@bunga_keluar_excel')->name('laporan.bunga_keluar-excel');
            Route::get('/bunga_datang', 'LaporanController@bunga_datang_index')->name('laporan.bunga_datang');
            Route::get('/bunga_datang-pdf', 'LaporanController@bunga_datang_pdf')->name('laporan.bunga_datang-pdf');
            Route::get('/bunga_datang-excel', 'LaporanController@bunga_datang_excel')->name('laporan.bunga_datang-excel');
        });

        Route::get('posts/{post}/log', 'PostController@log')->name('posts.log');
        Route::resource('posts', 'PostController');

       

    });


});