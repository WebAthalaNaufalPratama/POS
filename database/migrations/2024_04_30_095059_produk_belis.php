<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProdukBelis extends Migration
{
    /*
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produkbelis', function (Blueprint $table) {
            $table->id();
            $table->integer('pembelian_id')->nullable();
            $table->integer('produk_id')->nullable();
            $table->integer('jml_dikirim')->nullable();
            $table->integer('jml_diterima')->nullable();
            $table->integer('kondisi_id')->nullable();
            $table->integer('poinden_id')->nullable();
            $table->string('kode_produk_inden')->nullable();
            $table->integer('harga')->nullable();
            $table->integer('diskon')->nullable();
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
        Schema::dropIfExists('produkbelis');
    }
}
