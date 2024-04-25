<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_penjualan_id')->nullable();
            $table->integer('mutasi_id')->nullable();
            $table->integer('invoice_purchase_id')->nullable();
            $table->integer('retur_pembelian_id')->nullable();
            $table->string('no_invoice_bayar')->nullable();
            $table->integer('nominal')->nullable();
            $table->integer('rekening_id')->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->string('bukti')->nullable();
            $table->string('status_bayar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembayarans');
    }
}
