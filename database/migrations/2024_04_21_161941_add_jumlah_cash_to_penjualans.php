<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahCashToPenjualans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('jumlah_diskon')->nullable()->change();
            $table->renameColumn('jumlah_diskon', 'total_promo');
            $table->integer('jumlahCash')->after('rekening_id')->nullable();
            $table->integer('persen_ppn')->after('jenis_ppn')->nullable();
            $table->integer('dibuat_id')->after('sisa_bayar')->nullable();
            $table->date('tanggal_dibuat')->after('dibuat_id')->nullable();
            $table->integer('dibukukan_id')->after('tanggal_dibuat')->nullable();
            $table->date('tanggal_dibukukan')->after('dibukukan_id')->nullable();
            $table->integer('auditor_id')->after('tanggal_dibukukan')->nullable();
            $table->date('tanggal_audit')->after('auditor_id')->nullable();
            $table->string('cara_bayar')->change();
            $table->bigInteger('rekening_id')->nullable()->change();
            $table->string('pilih_pengiriman')->change();
            $table->bigInteger('ongkir_id')->nullable()->change();
            $table->string('alamat_tujuan')->nullable()->change();
            $table->integer('biaya_ongkir')->nullable()->change();
            $table->bigInteger('promo_id')->nullable()->change();
            $table->string('jenis_ppn')->nullable()->change();
            $table->integer('jumlah_ppn')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            //
        });
    }
}
