<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturpembeliansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('returpembelians', function (Blueprint $table) {
            $table->id();
            $table->integer('invoicepo_id');
            $table->date('tgl_retur');
            $table->string('komplain');
            $table->string('foto')->nullable();
            $table->integer('subtotal');
            $table->integer('ongkir')->nullable();
            $table->integer('total');
            $table->integer('pembuat');
            $table->string('status_dibuat');
            $table->integer('pembuku')->nullable();
            $table->string('status_dibuku')->nullable();
            $table->date('tgl_dibuat')->nullable();
            $table->date('tgl_dibuku')->nullable();
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
        Schema::dropIfExists('returpembelians');
    }
}
