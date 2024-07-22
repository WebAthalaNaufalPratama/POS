<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPenyetujuToReturPenjualans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retur_penjualans', function (Blueprint $table) {
            $table->integer('pemeriksa')->nullable()->after('pembuat');
            $table->integer('pembuku')->nullable()->after('pemeriksa');
            $table->renameColumn('dibukukan', 'tanggal_diperiksa');
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
            $table->dropColumn('pemeriksa');
            $table->dropColumn('pembuku');
            $table->dropColumn('tanggal_diperiksa');
        });
    }
}
