<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInInvoiceSewaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_sewas', function (Blueprint $table) {
            $table->unsignedInteger('ppn_persen')->default(0)->after('rekening_id');
            $table->unsignedBigInteger('ppn_nominal')->default(0)->after('ppn_persen');
            $table->unsignedInteger('pph_persen')->default(0)->after('ppn_nominal');
            $table->unsignedBigInteger('pph_nominal')->default(0)->after('pph_persen');
            $table->unsignedBigInteger('ongkir_nominal')->default(0)->after('pph_nominal');
            $table->unsignedBigInteger('promo_persen')->default(0)->after('ongkir_nominal');
            $table->unsignedBigInteger('total_promo')->default(0)->after('promo_persen');
            $table->unsignedBigInteger('subtotal')->default(0)->after('total_promo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_sewas', function (Blueprint $table) {
            $table->dropColumn(['ppn_persen', 'ppn_nominal', 'pph_persen', 'pph_nominal', 'ongkir_nominal', 'promo_persen', 'total_promo', 'subtotal']);
        });
    }
}
