<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiskonToProdukTerjuals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produk_terjuals', function (Blueprint $table) {
            $table->string('jenis_diskon')->nullable()->after('jumlah');
            $table->integer('diskon')->nullable()->after('jenis_diskon');
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
            $table->dropColumn('jenis_diskon');
            $table->dropColumn('diskon');
        });
    }
}
