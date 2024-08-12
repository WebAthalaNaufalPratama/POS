<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPenerimaColumnToReturindenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('returindens', function (Blueprint $table) {
            $table->dropColumn('penerima_id');
            $table->dropColumn('pemeriksa_id');
            $table->dropColumn('tgl_diterima_ttd');
            $table->dropColumn('tgl_diperiksa');
            $table->dropColumn('status_diterima');
            $table->dropColumn('status_diperiksa');
            $table->string('foto')->nullable()->after('no_retur');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('returindens', function (Blueprint $table) {
            $table->unsignedBigInteger('penerima_id')->nullable();
            $table->unsignedBigInteger('pemeriksa_id')->nullable();
            $table->date('tgl_diterima_ttd')->nullable();
            $table->date('tgl_diperiksa')->nullable();
            $table->string('status_diterima')->nullable();
            $table->string('status_diperiksa')->nullable();
            $table->dropColumn('foto');
        });
    }
}
