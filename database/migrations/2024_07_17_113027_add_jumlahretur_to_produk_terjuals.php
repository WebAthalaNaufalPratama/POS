<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahreturToProdukTerjuals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produk_terjuals', function (Blueprint $table) {
            $table->integer('jumlahretur')->nullable()->after('jumlah');
            $table->integer('diskonretur')->nullable()->after('jumlahretur');
            $table->integer('hargajualretur')->nullable()->after('diskonretur');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produk_terjuals', function (Blueprint $table) {
            $table->dropColumn('jumlahretur');
            $table->dropColumn('diskonretur');
            $table->dropColumn('hargajualretur');
        });
    }
}
