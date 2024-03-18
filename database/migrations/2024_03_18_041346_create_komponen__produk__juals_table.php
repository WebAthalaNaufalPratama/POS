<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKomponenProdukJualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('komponen_produk_juals', function (Blueprint $table) {
            $table->id();
            $table->integer('produk_jual_id');
            $table->string('kode_produk');
            $table->string('nama_produk');
            $table->string('tipe_produk');
            $table->integer('kondisi');
            $table->text('deskripsi');
            $table->integer('jumlah');
            $table->string('harga_satuan');
            $table->string('harga_total');
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
        Schema::dropIfExists('komponen_produk_juals');
    }
}
