<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Poinden extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poinden', function (Blueprint $table) {
            $table->id();
            $table->string('no_po');
            $table->integer('supplier_id');
            $table->integer('produkbeli_id');
            $table->date('bulan_inden');
            $table->integer('pembuat');
            $table->integer('pemeriksa')->nullable();
            $table->datetime('tgl_dibuat');
            $table->datetime('tgl_diperiksa')->nullable();
            $table->string('status_dibuat');
            $table->string('status_diperiksa')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
