<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_do')->unique();
            $table->string('no_sewa');
            $table->string('jenis_do');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('customer_id');
            $table->string('handphone');
            $table->string('penerima');
            $table->text('alamat');
            $table->text('catatan')->nullable();
            $table->string('status');
            $table->text('file')->nullable();
            $table->integer('driver');
            $table->integer('pembuat');
            $table->integer('penyetuju')->nullable();
            $table->integer('pemeriksa')->nullable();
            $table->datetime('tanggal_driver')->nullable();
            $table->datetime('tanggal_pembuat')->nullable();
            $table->datetime('tanggal_penyetuju')->nullable();
            $table->datetime('tanggal_pemeriksa')->nullable();
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
        Schema::dropIfExists('delivery_orders');
    }
}
