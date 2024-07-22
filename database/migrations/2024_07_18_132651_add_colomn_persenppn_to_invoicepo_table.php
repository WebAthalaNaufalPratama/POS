<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColomnPersenppnToInvoicepoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoicepo', function (Blueprint $table) {
            $table->integer('persen_ppn')->nullable()->after('diskon');

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
            $table->dropColumn('persen_ppn');
        });
    }
}
