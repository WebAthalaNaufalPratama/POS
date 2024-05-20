<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoreturToProdukTerjuals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produk_terjuals', function (Blueprint $table) {
            $table->string('no_retur')->nullable()->after('no_form');
            $table->string('alasan')->nullable()->after('harga');
            $table->string('no_mutasigo')->nullable()->after('no_retur');
            $table->string('no_mutasiog')->nullable()->after('no_mutasigo');
            $table->string('no_mutasigg')->nullable()->after('no_mutasiog');
            $table->integer('jumlah_dikirim')->nullable()->after('alasan');
            $table->integer('jumlah_diterima')->nullable()->after('jumlah_dikirim');
            $table->integer('kondisi_dikirim')->nullable()->after('jumlah_diterima');
            $table->integer('kondisi_diterima')->nullable()->after('kondisi_dikirim');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produk_terjuals', function (Blueprint $table) {
            //
        });
    }
}
