<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdukTerjualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produk_terjuals', function (Blueprint $table) {
            $table->id();
            $table->integer('produk_jual_id');
            $table->string('no_invoice')->nullable();
            $table->string('no_do')->nullable();
            $table->string('no_sewa')->nullable();
            $table->string('no_mutasi')->nullable();
            $table->string('no_form')->nullable();
            $table->string('harga');
            $table->integer('jumlah');
            $table->string('harga_jual');
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
        Schema::dropIfExists('produk_terjuals');
    }
}
