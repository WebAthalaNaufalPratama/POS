<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKembaliSewasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kembali_sewas', function (Blueprint $table) {
            $table->id();
            $table->string('no_kembali')->unique();
            $table->string('no_sewa');
            $table->date('tanggal_kembali');
            $table->text('file')->nullable();
            $table->string('status');
            $table->integer('driver');
            $table->integer('pembuat');
            $table->integer('penyetuju')->nullable();
            $table->integer('pemeriksa')->nullable();
            $table->datetime('tanggal_driver')->nullable();
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
        Schema::dropIfExists('kembali_sewas');
    }
}
