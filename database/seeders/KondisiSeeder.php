<?php

namespace Database\Seeders;

use App\Models\Kondisi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KondisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Kondisi::truncate();
        
        DB::statement('ALTER TABLE kondisis AUTO_INCREMENT = 1');

        Kondisi::insert([
            ['nama' => 'Baik', 'deskripsi' => 'Barang dalam kondisi baik', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Afkir', 'deskripsi' => 'Barang dalam kondisi rusak', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Bonggol', 'deskripsi' => 'Barang hanya akar tanpa bunga', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
