<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOngkirIdToMutasis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mutasis', function (Blueprint $table) {
            $table->string('bukti')->nullable()->after('tanggal_diterima');
            $table->string('pilih_pengiriman')->nullable()->after('bukti');
            $table->string('alamat_tujuan')->nullable()->after('pilih_pengiriman');
            $table->integer('ongkir_id')->nullable()->after('alamat_tujuan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mutasis', function (Blueprint $table) {
            //
        });
    }
}
