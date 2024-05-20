<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdukReturTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produk_retur', function (Blueprint $table) {
            $table->id();
            $table->integer('produk_id');
            $table->string('alasan')->nullable();
            $table->integer('jumlah');
            $table->integer('harga')->nullable();
            $table->integer('diskon')->nullable();
            $table->integer('totharga')->nullable();
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
        Schema::dropIfExists('produk_retur');
    }
}
