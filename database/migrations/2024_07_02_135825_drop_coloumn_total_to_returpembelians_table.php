<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColoumnTotalToReturpembeliansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('returpembelians', function (Blueprint $table) {
            $table->dropColumn('total');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('returpembelians', function (Blueprint $table) {
            $table->integer('total')->nullable()->after('ongkir');

        });
    }
}
