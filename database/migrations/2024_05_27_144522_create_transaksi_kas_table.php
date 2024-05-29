<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiKasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_kas', function (Blueprint $table) {
            $table->id();
            $table->integer('akun_id');
            $table->integer('lokasi_id');
            $table->text('keterangan');
            $table->integer('kuantitas');
            $table->integer('harga_satuan');
            $table->integer('harga_total');
            $table->date('tanggal_transaksi');
            $table->string('status');
            $table->string('bukti');
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
        Schema::dropIfExists('transaksi_kas');
    }
}
