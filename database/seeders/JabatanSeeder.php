<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Jabatan::truncate();
        
        DB::statement('ALTER TABLE jabatans AUTO_INCREMENT = 1');

        Jabatan::insert([
            ['nama' => 'Driver', 'deskripsi' => 'Pengantar barang', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Perangkai', 'deskripsi' => 'Perangkai prduk', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Sales', 'deskripsi' => 'Menjualkan produk', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
