<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryGreenHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_green_houses', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk');
            $table->integer('kondisi_id');
            $table->integer('lokasi_id');
            $table->integer('jumlah');
            $table->integer('min_stok');
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
        Schema::dropIfExists('inventory_green_houses');
    }
}
