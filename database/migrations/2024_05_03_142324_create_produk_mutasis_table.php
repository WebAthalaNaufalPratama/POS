<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdukMutasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produk_mutasis', function (Blueprint $table) {
            $table->id();
            $table->integer('produk_jual_id')->nullable();
            $table->string('no_mutasi')->nullable();
            $table->integer('jumlah_dikirim')->nullable();
            $table->integer('jumlah_diterima')->nullable();
            $table->integer('kondisi_diterima')->nullable();
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
        Schema::dropIfExists('produk_mutasis');
    }
}
