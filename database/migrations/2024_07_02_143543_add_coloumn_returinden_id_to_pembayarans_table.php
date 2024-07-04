<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColoumnReturindenIdToPembayaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->integer('returinden_id')->nullable()->after('retur_pembelian_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn('returinden_id');
        });
    }
}
