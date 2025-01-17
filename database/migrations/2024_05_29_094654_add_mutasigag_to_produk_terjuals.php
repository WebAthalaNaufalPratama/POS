<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMutasigagToProdukTerjuals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produk_terjuals', function (Blueprint $table) {
            $table->string('no_mutasigag')->nullable()->after('no_mutasigg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produk_terjuals', function (Blueprint $table) {
            $table->dropColumn('no_mutasigag');
        });
    }
}
