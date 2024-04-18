<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToPromoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->string('ketentuan_produk')->nullable()->after('ketentuan');
            $table->string('ketentuan_tipe_produk')->nullable()->after('ketentuan_produk');
            $table->string('ketentuan_min_transaksi')->nullable()->after('ketentuan_tipe_produk');
            $table->string('diskon_free_produk')->nullable()->after('diskon');
            $table->string('diskon_nominal')->nullable()->after('diskon_free_produk');
            $table->string('diskon_persen')->nullable()->after('diskon_nominal');
            $table->string('diskon_poin')->nullable()->after('diskon_persen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn('ketentuan_produk');
            $table->dropColumn('ketentuan_tipe_produk');
            $table->dropColumn('ketentuan_min_transaksi');
            $table->dropColumn('diskon_free_produk');
            $table->dropColumn('diskon_nominal');
            $table->dropColumn('diskon_persen');
            $table->dropColumn('diskon_poin');
        });
    }
}
