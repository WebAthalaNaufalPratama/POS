<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMutasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutasis', function (Blueprint $table) {
            $table->id();
            $table->integer('pengirim')->nullable();
            $table->integer('penerima')->nullable();
            $table->string('no_mutasi')->nullable();
            $table->string('status')->nullable();
            $table->date('tanggal_kirim')->nullable();
            $table->date('tanggal_diterima')->nullable();
            $table->integer('biaya_pengiriman')->nullable();
            $table->integer('total_biaya')->nullable();
            $table->integer('pembuat_id')->nullable();
            $table->integer('penerima_id')->nullable();
            $table->integer('dibukukan_id')->nullable();
            $table->integer('diperiksa_id')->nullable();
            $table->date('tanggal_pembuat')->nullable();
            $table->date('tanggal_penerima')->nullable();
            $table->date('tanggal_dibukukan')->nullable();
            $table->date('tanggal_diperiksa')->nullable();
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
        Schema::dropIfExists('mutasis');
    }
}
