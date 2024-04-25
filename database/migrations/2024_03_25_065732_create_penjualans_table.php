<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenjualansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_customer');
            $table->integer('point_dipakai');
            $table->bigInteger('lokasi_id');
            $table->string('distribusi');
            $table->string('no_invoice');
            $table->date('tanggal_invoice');
            $table->date('jatuh_tempo');
            $table->bigInteger('employee_id');
            $table->string('status');
            $table->string('bukti_file');
            $table->string('notes');
            $table->integer('cara_bayar');
            $table->bigInteger('rekening_id');
            $table->integer('pilih_pengiriman');
            $table->bigInteger('ongkir_id');
            $table->string('alamat_tujuan');
            $table->integer('biaya_ongkir');
            $table->integer('sub_total');
            $table->bigInteger('promo_id');
            $table->integer('jumlah_diskon');
            $table->integer('jenis_ppn');
            $table->integer('jumlah_ppn');
            $table->integer('total_tagihan');
            $table->integer('dp');
            $table->integer('sisa_bayar');
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
        Schema::dropIfExists('penjualans');
    }
}
