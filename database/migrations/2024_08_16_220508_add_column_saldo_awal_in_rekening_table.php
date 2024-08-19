<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSaldoAwalInRekeningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rekenings', function (Blueprint $table) {
            $table->unsignedBigInteger('saldo_awal')->default(0)->after('lokasi_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rekenings', function (Blueprint $table) {
            $table->dropColumn('saldo_awal');
        });
    }
}
