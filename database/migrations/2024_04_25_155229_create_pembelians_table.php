<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembeliansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('no_po');
            $table->integer('supplier_id');
            $table->integer('lokasi_id');
            $table->date('tgl_kirim');
            $table->string('no_do_suplier')->nullable();
            $table->string('file_do_suplier')->nullable();
            $table->integer('pembuat');
            $table->integer('pemeriksa')->nullable();
            $table->integer('penerima')->nullable();
            $table->datetime('tgl_dibuat');
            $table->datetime('tgl_diterima')->nullable();
            $table->datetime('tgl_diperiksa')->nullable();
            $table->string('status_dibuat');
            $table->string('status_diterima')->nullable();
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
        Schema::dropIfExists('pembelians');
    }
}
