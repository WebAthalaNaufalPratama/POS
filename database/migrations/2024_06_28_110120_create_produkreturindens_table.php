<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdukreturindensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produkreturindens', function (Blueprint $table) {
            $table->id();
            $table->integer('returinden_id');
            $table->integer('produk_mutasi_inden_id');
            $table->text('alasan')->nullable();
            $table->integer('jml_diretur');
            $table->integer('harga_satuan');
            $table->integer('totalharga');
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
        Schema::dropIfExists('produkreturindens');
    }
}
