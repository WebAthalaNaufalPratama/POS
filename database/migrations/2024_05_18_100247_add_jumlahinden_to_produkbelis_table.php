<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahindenToProdukbelisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produkbelis', function (Blueprint $table) {
            $table->integer('jumlahInden')->after('kode_produk_inden')->nullable();
            $table->string('keterangan')->after('jumlahInden')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produkbelis', function (Blueprint $table) {
            $table->dropColumn('jumlahInden');
            $table->dropColumn('keterangan');
        });
    }
}
