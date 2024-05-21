<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryIndensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_indens', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk_inden')->nullable();
            $table->string('bulan_inden')->nullable();
            $table->integer('produk_id')->nullable();
            $table->integer('jumlah')->nullable();
            $table->integer('harga')->nullable();
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
        Schema::dropIfExists('inventory_indens');
    }
}
