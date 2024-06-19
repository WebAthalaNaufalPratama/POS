<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameReturpembelianIdToProdukreturTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produk_retur', function (Blueprint $table) {
            $table->renameColumn('returpembelians_id', 'returpembelian_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produk_retur', function (Blueprint $table) {
            $table->renameColumn('returpembelian_id', 'returpembelians_id');

        });
    }
}
