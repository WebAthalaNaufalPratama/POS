<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicepo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoicepo', function (Blueprint $table) {
            $table->id();
            $table->integer('pembelian_id')->nullable();
            $table->integer('poinden_id')->nullable();
            $table->date('tgl_inv');
            $table->string('no_inv');
            $table->integer('pembuat');
            $table->string('status_dibuat');
            $table->integer('pembuku');
            $table->string('status_dibuku');
            $table->date('tgl_dibuat');
            $table->date('tgl_dibukukan');
            $table->integer('subtotal');
            $table->integer('diskon');
            $table->integer('ppn');
            $table->integer('biaya_kirim');
            $table->integer('total_tagihan');
            $table->integer('dp');
            $table->integer('sisa');
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
        Schema::dropIfExists('invoicepo');
    }
}
