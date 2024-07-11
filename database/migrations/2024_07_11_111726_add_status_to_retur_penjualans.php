<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToReturPenjualans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retur_penjualans', function (Blueprint $table) {
            $table->string('status')->nullable()->after('komplain');
            $table->string('alasan_batal')->nullable()->after('status');
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
            $table->dropColumn('status');
            $table->dropColumn('alasan');
        });
    }
}
