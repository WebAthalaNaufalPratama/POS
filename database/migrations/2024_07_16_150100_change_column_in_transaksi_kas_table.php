<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnInTransaksiKasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->foreignId('lokasi_penerima')->nullable()->after('id');
            $table->foreignId('lokasi_pengirim')->nullable()->after('lokasi_penerima');
            $table->foreignId('rekening_penerima')->nullable()->after('lokasi_pengirim');
            $table->foreignId('rekening_pengirim')->nullable()->after('rekening_penerima');
            $table->unsignedBigInteger('nominal')->default(0)->after('rekening_pengirim');
            $table->unsignedBigInteger('biaya_lain')->default(0)->after('nominal');
            $table->date('tanggal')->after('biaya_lain');
            $table->text('jenis')->after('tanggal');
            $table->text('file')->nullable()->after('jenis');
            $table->dropColumn(['akun_id', 'lokasi_id', 'kuantitas', 'harga_satuan', 'harga_total', 'tanggal_transaksi', 'bukti']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropColumn(['lokasi_penerima', 'lokasi_pengirim', 'rekening_penerima', 'rekening_pengirim', 'nominal', 'tanggal', 'file', 'jenis', 'biaya_lain']);
            $table->integer('akun_id')->after('id');
            $table->integer('lokasi_id')->after('akun_id');
            $table->integer('kuantitas')->after('lokasi_id');
            $table->integer('harga_satuan')->after('kuantitas');
            $table->integer('harga_total')->after('harga_satuan');
            $table->date('tanggal_transaksi')->after('harga_total');
            $table->string('bukti')->after('tanggal_transaksi');
        });
    }
}
