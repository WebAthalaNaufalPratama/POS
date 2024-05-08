<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNoKembaliSewaToProdukTerjualTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produk_terjuals', function (Blueprint $table) {
            $table->string('no_kembali_sewa')->nullable()->after('no_form');
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
            $table->dropColumn('no_kembali_sewa');
        });
    }
}
