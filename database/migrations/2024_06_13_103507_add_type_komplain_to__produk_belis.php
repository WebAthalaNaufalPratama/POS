<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeKomplainToProdukBelis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ProdukBelis', function (Blueprint $table) {
            $table->string('type_komplain')->nullable()->after('totalharga');
            $table->integer('diskon_retur')->nullable()->after('type_komplain');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ProdukBelis', function (Blueprint $table) {
            $table->dropColumn('type_komplain');
            $table->dropColumn('diskon_retur');
        });
    }
}
