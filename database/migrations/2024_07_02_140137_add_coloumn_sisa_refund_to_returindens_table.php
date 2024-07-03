<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColoumnSisaRefundToReturindensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('returindens', function (Blueprint $table) {
        $table->integer('sisa_refund')->nullable()->after('refund');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('returindens', function (Blueprint $table) {
            $table->dropColumn('sisa_refund');
        });
    }
}
