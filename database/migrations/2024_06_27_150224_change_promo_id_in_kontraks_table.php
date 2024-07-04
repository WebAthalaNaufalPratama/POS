<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePromoIdInKontraksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->string('promo_persen')->after('ongkir_nominal');
            $table->dropColumn('promo_id');
            $table->dropColumn('ongkir_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->dropColumn('promo_persen');
            $table->integer('promo_id')->nullable()->after('ongkir_nominal');
            $table->string('ongkir_id')->after('pph_nominal');
        });
    }
}
