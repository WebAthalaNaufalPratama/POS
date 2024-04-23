<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormPerangkaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_perangkais', function (Blueprint $table) {
            $table->id();
            $table->string('no_form')->unique();
            $table->string('jenis_rangkaian');
            $table->date('tanggal');
            $table->integer('perangkai_id');
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
        Schema::dropIfExists('form_perangkais');
    }
}
