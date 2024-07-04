<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturindensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('returindens', function (Blueprint $table) {
            $table->id();
            $table->integer('mutasiinden_id');
            $table->string('no_retur');
            $table->integer('refund');
            $table->integer('total_akhir');
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
        Schema::dropIfExists('returindens');
    }
}
