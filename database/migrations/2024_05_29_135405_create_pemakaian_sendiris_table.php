<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemakaianSendirisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pemakaian_sendiris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lokasi_id');
            $table->foreignId('produk_id');
            $table->foreignId('kondisi_id');
            $table->foreignId('karyawan_id');
            $table->integer('jumlah');
            $table->text('alasan');
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
        Schema::dropIfExists('pemakaian_sendiris');
    }
}
