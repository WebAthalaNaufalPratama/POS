<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToProdukTerjualTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produk_terjuals', function (Blueprint $table) {
            $table->string('satuan')->nullable()->after('jumlah');
            $table->string('keterangan')->nullable()->after('satuan');
            $table->string('jenis')->nullable()->after('keterangan');
            $table->string('detail_lokasi')->nullable()->after('jenis');
            $table->string('harga')->nullable()->change();
            $table->string('harga_jual')->nullable()->change();
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
            $table->dropColumn('satuan');
            $table->dropColumn('detail_lokasi');
            $table->string('harga')->change();
            $table->string('harga_jual')->change();
        });
    }
}
