<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubtotalreturToPenjualans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->integer('biaya_kirim_retur')->nullable()->after('biaya_ongkir');
            $table->integer('sub_total_retur')->nullable()->after('sub_total');
            $table->integer('jumlahppnretur')->nullable()->after('jumlah_ppn');
            $table->integer('total_tagihan_retur')->nullable()->after('total_tagihan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn('biaya_kirim_retur');
            $table->dropColumn('sub_total_retur');
            $table->dropColumn('jumlahppnretur');
            $table->dropColumn('total_tagihan_retur');
        });
    }
}
