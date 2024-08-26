<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTanggalSelesaiToKontrakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->datetime('selesai_pembuat')->nullable()->after('tanggal_pemeriksa');
            $table->datetime('selesai_auditor')->nullable()->after('selesai_pembuat');
            $table->datetime('selesai_finance')->nullable()->after('selesai_auditor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->dropColumn(['selesai_pembuat', 'selesai_auditor', 'selesai_finance']);
        });
    }
}
