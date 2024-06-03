<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnDpSisaToInvoicepoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoicepo', function (Blueprint $table) {
            $table->integer('dp')->nullable()->change();
            $table->integer('sisa')->nullable()->change();
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
            $table->integer('dp')->nullable(false)->change();
            $table->integer('sisa')->nullable(false)->change();
        });
    }
}
