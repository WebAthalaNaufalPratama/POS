<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAlamatTujuanToReturPenjualans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retur_penjualans', function (Blueprint $table) {
            $table->string('alamat_tujuan')->nullable()->after('pilih_pengiriman');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retur_penjualans', function (Blueprint $table) {
            $table->dropColumn('alamat_tujuan');
        });
    }
}
