<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKondisiditerimaToKomponenProdukTerjuals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('komponen_produk_terjuals', function (Blueprint $table) {
            $table->integer('kondisi_diterima')->nullable()->after('kondisi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('komponen_produk_terjuals', function (Blueprint $table) {
            $table->dropColumn('kondisi_diterima');
        });
    }
}
