<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeNullableInRekeningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rekenings', function (Blueprint $table) {
            $table->enum('jenis', ['Rekening', 'Cash'])->default('Rekening')->after('id');
            $table->string('bank')->nullable()->change();
            $table->string('nomor_rekening')->nullable()->change();
            $table->string('nama_akun')->nullable()->change();
            $table->unsignedBigInteger('saldo_akhir')->default(0)->after('saldo_awal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rekenings', function (Blueprint $table) {
            DB::table('rekenings')->whereNull('bank')->update(['bank' => '']);
            DB::table('rekenings')->whereNull('nomor_rekening')->update(['nomor_rekening' => '']);
            DB::table('rekenings')->whereNull('nama_akun')->update(['nama_akun' => '']);

            $table->dropColumn('jenis');
            
            $table->string('bank')->nullable(false)->change();
            $table->string('nomor_rekening')->nullable(false)->change();
            $table->string('nama_akun')->nullable(false)->change();
            
            $table->dropColumn('saldo_akhir');
        });
    }
}
