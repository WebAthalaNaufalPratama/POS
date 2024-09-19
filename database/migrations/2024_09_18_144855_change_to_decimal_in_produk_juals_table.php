<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeToDecimalInProdukJualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('komponen_produk_juals', function (Blueprint $table) {
            $table->decimal('jumlah', 8, 2)->change();
        });
        Schema::table('komponen_produk_terjuals', function (Blueprint $table) {
            $table->decimal('jumlah', 8, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('komponen_produk_juals', function (Blueprint $table) {
            $table->integer('jumlah')->change();
        });
        Schema::table('komponen_produk_terjuals', function (Blueprint $table) {
            $table->integer('jumlah')->change();
        });
    }
}
