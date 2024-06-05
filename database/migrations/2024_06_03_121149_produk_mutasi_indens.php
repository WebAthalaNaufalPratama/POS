<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProdukMutasiIndens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produk_mutasi_indens', function (Blueprint $table) {
            $table->id();
            $table->integer('mutasiinden_id')->nullable();
            $table->integer('inventoryinden_id')->nullable();
            $table->integer('jml_dikirim')->nullable();
            $table->integer('jml_diterima')->nullable();
            $table->integer('kondisi_id')->nullable();
            $table->integer('biaya_rawat')->nullable();
            $table->integer('totalharga')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produk_mutasi_indens');
    }
}
