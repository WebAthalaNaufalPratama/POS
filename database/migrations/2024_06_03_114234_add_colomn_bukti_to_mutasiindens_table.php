<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColomnBuktiToMutasiindensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mutasiindens', function (Blueprint $table) {
            $table->string('bukti')->nullable()->after('sisa_bayar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mutasiindens', function (Blueprint $table) {
            $table->dropColumn('bukti');
        });
    }
}
