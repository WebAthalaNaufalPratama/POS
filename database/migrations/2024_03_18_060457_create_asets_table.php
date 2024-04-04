<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asets', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('deskripsi');
            $table->bigInteger('lokasi_id');
            $table->integer('jumlah');
            $table->date('tahun_beli');
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
        Schema::dropIfExists('asets');
    }
}
