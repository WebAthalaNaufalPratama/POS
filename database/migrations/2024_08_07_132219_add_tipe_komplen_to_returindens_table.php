<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipeKomplenToReturindensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('returindens', function (Blueprint $table) {
            $table->string('tipe_komplain')->nullable()->after('foto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('returindens', function (Blueprint $table) {
            $table->dropColumn('tipe_komplain');
        });
    }
}
