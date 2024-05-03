<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdukReturJualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produk_retur_juals', function (Blueprint $table) {
            $table->id();
            $table->integer('produk_jual_id')->nullable();
            $table->string('no_retur')->nullable();
            $table->string('jenis')->nullable();
            $table->string('alasan')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('satuan')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('jenis_diskon')->nullable();
            $table->integer('diskon')->nullable();
            $table->integer('harga')->nullable();
            $table->integer('total_harga')->nullable();
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
        Schema::dropIfExists('produk_retur_juals');
    }
}
