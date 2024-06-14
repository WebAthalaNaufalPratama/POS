<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameProdukIdInTableProdukRetur extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produk_retur', function (Blueprint $table) {
            $table->renameColumn('produk_id', 'produkbeli_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produk_retur', function (Blueprint $table) {
            $table->renameColumn('produkbeli_id', 'produk_id');
        });
    }
}
