<?php

namespace Database\Seeders;

use App\Models\Tipe_Lokasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Tipe_LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tipe_Lokasi::truncate();
        
        DB::statement('ALTER TABLE tipe_lokasis AUTO_INCREMENT = 1');

        Tipe_Lokasi::insert([
            ['nama' => 'Galery', 'deskripsi' => 'Tempat menyimpan barang per daerah', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Outlet', 'deskripsi' => 'Turunan dari galery', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Greenhouse', 'deskripsi' => 'Tempat menanam tanaman (pusat)', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Gudang', 'deskripsi' => 'Penyimpanan barang (pusat)', 'created_at' => now(), 'updated_at' => now()],
            
            ['nama' => 'Pusat', 'deskripsi' => 'untuk kas', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
