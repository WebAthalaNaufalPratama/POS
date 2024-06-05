<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMutasiindensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutasiindens', function (Blueprint $table) {
            $table->id();
            $table->string('no_mutasi');
            $table->integer('supplier_id');
            $table->integer('lokasi_id');
            $table->date('tgl_dikirim');
            $table->date('tgl_diterima')->nullable();
            $table->integer('biaya_pengiriman')->nullable();
            $table->integer('biaya_perawatan')->nullable();
            $table->integer('total_biaya')->nullable();
            $table->integer('sisa_bayar')->nullable();
            $table->integer('pembuat_id')->nullable();
            $table->integer('penerima_id')->nullable();
            $table->integer('pembuku_id')->nullable();
            $table->integer('pemeriksa_id')->nullable();
            $table->date('tgl_dibuat')->nullable();
            $table->date('tgl_diterima_ttd')->nullable();
            $table->date('tgl_dibukukan')->nullable();
            $table->date('tgl_diperiksa')->nullable();
            $table->string('status_dibuat')->nullable();
            $table->string('status_diterima')->nullable();
            $table->string('status_dibukukan')->nullable();
            $table->string('status_diperiksa')->nullable();
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
        Schema::dropIfExists('mutasiindens');
    }
}
