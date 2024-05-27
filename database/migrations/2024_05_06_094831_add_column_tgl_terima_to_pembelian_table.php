<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTglTerimaToPembelianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pembelians', function (Blueprint $table) {
                $table->datetime('tgl_diterima_ttd')->nullable()->after('tgl_dibuat');
                $table->date('tgl_diterima')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('tgl_diterima_ttd');
            $table->datetime('tgl_diterima')->nullable()->change();
        });
    }
}
