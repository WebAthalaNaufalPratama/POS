<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToDeliveryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->string('pic')->nullable()->after('handphone');
            $table->string('penerima')->nullable()->change();
            $table->renameColumn('no_sewa', 'no_referensi');
            $table->renameColumn('tanggal_mulai', 'tanggal_kirim');
            $table->dropColumn('tanggal_selesai');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn('pic');
            $table->string('penerima')->nullable(false)->change();
            $table->renameColumn('no_referensi', 'no_sewa');
            $table->renameColumn('tanggal_kirim', 'tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
        });
    }
}
