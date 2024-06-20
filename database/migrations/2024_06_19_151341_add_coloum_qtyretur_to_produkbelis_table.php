<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColoumQtyreturToProdukbelisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produkbelis', function (Blueprint $table) {
            // Mengubah nama kolom diskon_retur menjadi qty_retur dan menjadikannya nullable
            $table->renameColumn('diskon_retur', 'qty_komplain');
            // Mengubah kolom qty_retur menjadi nullable
            // $table->integer('qty_retur')->nullable()->change();
            // Menambahkan kolom baru qty_diskon dan qty_refund yang dapat bernilai null
            // $table->integer('qty_diskon')->nullable()->after('qty_retur'); // tambahkan setelah kolom 'qty_retur'
            // $table->integer('qty_refund')->nullable()->after('qty_diskon'); // tambahkan setelah kolom 'qty_diskon'
        });
    }

    /**
     * Balikkan migrasi.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produkbelis', function (Blueprint $table) {
            // Mengubah nama kolom qty_retur kembali menjadi diskon_retur dan menjadikannya non-nullable
            $table->renameColumn('qty_retur', 'diskon_retur');
            // Mengubah kolom diskon_retur menjadi non-nullable
            // $table->integer('diskon_retur')->nullable(false)->change();
            // Menghapus kolom qty_diskon dan qty_refund
            // $table->dropColumn('qty_diskon');
            // $table->dropColumn('qty_refund');
        });
    }

}
