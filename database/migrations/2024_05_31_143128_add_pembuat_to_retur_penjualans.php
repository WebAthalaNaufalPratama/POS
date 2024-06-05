<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPembuatToReturPenjualans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retur_penjualans', function (Blueprint $table) {
            $table->integer('pembuat')->nullable()->after('total');
            $table->date('tanggal_pembuat')->nullable()->after('pembuat');
            $table->integer('dibukukan')->nullable()->after('tanggal_pembuat');
            $table->date('tanggal_dibukukan')->nullable()->after('dibukukan');
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
            $table->dropColumn('pembuat');
            $table->dropColumn('tanggal_pembuat');
            $table->dropColumn('dibukukan');
            $table->dropColumn('tanggal_dibukukan');
        });
    }
}
