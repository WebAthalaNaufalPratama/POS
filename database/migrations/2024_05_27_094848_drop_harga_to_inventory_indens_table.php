<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropHargaToInventoryIndensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_indens', function (Blueprint $table) {
            $table->dropColumn('harga');
            $table->dropColumn('produk_id');
            $table->string('kode_produk')->after('bulan_inden');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_indens', function (Blueprint $table) {
            $table->integer('harga')->nullable();
            $table->integer('produk_id')->nullable();
            $table->dropColumn('kode_produk');
        });
    }
}
