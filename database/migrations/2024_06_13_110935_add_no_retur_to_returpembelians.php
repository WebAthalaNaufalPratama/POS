<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoReturToReturpembelians extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('returpembelians', function (Blueprint $table) {
            $table->string('no_retur')->nullable()->after('invoicepo_id');
            $table->string('catatan')->nullable()->after('no_retur');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('returpembelians', function (Blueprint $table) {
            //
        });
    }
}
