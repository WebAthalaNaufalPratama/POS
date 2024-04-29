<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturPenjualansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retur_penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('no_retur')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('lokasi_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->string('bukti')->nullable();
            $table->date('tanggal_invoice')->nullable();
            $table->string('no_invoice')->nullable();
            $table->date('tanggal_retur')->nullable();
            $table->string('no_do')->nullable();
            $table->string('komplain')->nullable();
            $table->string('catatan_komplain')->nullable();
            $table->string('pilih_pengiriman')->nullable();
            $table->integer('ongkir_id')->nullable();
            $table->integer('biaya_pengiriman')->nullable();
            $table->integer('total')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retur_penjualans');
    }
}
