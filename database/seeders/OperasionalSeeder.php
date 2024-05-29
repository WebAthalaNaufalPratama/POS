<?php

namespace Database\Seeders;

use App\Models\Operasional;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperasionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Operasional::truncate();
        
        DB::statement('ALTER TABLE operasionals AUTO_INCREMENT = 1');

        Operasional::insert([
            ['nama' => 'PUSAT', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'SEMARANG', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'SURABAYA', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'YOGYAKARTA', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
