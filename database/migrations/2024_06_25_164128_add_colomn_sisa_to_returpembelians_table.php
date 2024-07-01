<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColomnSisaToReturpembeliansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('returpembelians', function (Blueprint $table) {
            $table->integer('sisa')->nullable()->after('subtotal');

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
            $table->dropColumn('sisa');

        });
    }
}
