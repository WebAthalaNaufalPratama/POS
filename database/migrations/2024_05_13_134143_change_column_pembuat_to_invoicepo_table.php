<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnPembuatToInvoicepoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoicepo', function (Blueprint $table) {
            $table->integer('pembuat')->nullable()->change();
            $table->string('status_dibuat')->nullable()->change();
            $table->integer('pembuku')->nullable()->change();
            $table->string('status_dibuku')->nullable()->change();
            $table->date('tgl_dibuat')->nullable()->change();
            $table->date('tgl_dibukukan')->nullable()->change();
            $table->integer('diskon')->nullable()->change();
            $table->integer('ppn')->nullable()->change();
            $table->integer('biaya_kirim')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoicepo', function (Blueprint $table) {
            //
        });
    }
}
