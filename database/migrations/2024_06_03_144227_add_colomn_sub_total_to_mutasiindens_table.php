<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColomnSubTotalToMutasiindensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mutasiindens', function (Blueprint $table) {
            $table->string('subtotal')->nullable()->after('tgl_diterima');
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
            $table->dropColumn('subtotal');
        });
    }
}
