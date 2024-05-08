<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceSewasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_sewas', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->string('no_sewa');
            $table->date('tanggal_invoice');
            $table->date('jatuh_tempo');
            $table->text('catatan')->nullable();
            $table->integer('rekening_id');
            $table->string('total_tagihan');
            $table->string('dp')->default(0);
            $table->string('sisa_bayar');
            $table->string('status');
            $table->integer('sales');
            $table->integer('pembuat');
            $table->integer('penyetuju')->nullable();
            $table->integer('pemeriksa')->nullable();
            $table->datetime('tanggal_sales')->nullable();
            $table->datetime('tanggal_pembuat')->nullable();
            $table->datetime('tanggal_penyetuju')->nullable();
            $table->datetime('tanggal_pemeriksa')->nullable();
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
        Schema::dropIfExists('invoice_sewas');
    }
}
