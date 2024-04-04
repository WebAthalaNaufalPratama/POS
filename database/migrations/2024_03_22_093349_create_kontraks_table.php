<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKontraksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kontraks', function (Blueprint $table) {
            $table->id();
            $table->string('no_kontrak');
            $table->integer('lokasi_id');
            $table->integer('masa_sewa');
            $table->date('tanggal_kontrak');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('customer_id');
            $table->string('pic');
            $table->string('handphone');
            $table->text('alamat');
            $table->string('no_npwp');
            $table->string('nama_npwp');
            $table->integer('rekening_id');
            $table->text('catatan')->nullable();
            $table->string('ppn_persen');
            $table->string('ppn_nominal');
            $table->string('pph_persen');
            $table->string('pph_nominal');
            $table->string('ongkir_id');
            $table->string('ongkir_nominal');
            $table->integer('promo_id')->nullable();
            $table->string('total_promo')->default('0');
            $table->string('subtotal');
            $table->string('total_harga');
            $table->string('status');
            $table->text('file')->nullable();
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
        Schema::dropIfExists('kontraks');
    }
}
