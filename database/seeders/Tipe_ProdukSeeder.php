<?php

namespace Database\Seeders;

use App\Models\Tipe_Produk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Tipe_ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tipe_Produk::truncate();
        
        DB::statement('ALTER TABLE tipe_produks AUTO_INCREMENT = 1');

        Tipe_Produk::insert([
            ['nama' => 'Bunga', 'deskripsi' => 'Produk bunga', 'kategori' => 'Master', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Pot', 'deskripsi' => 'Produk pot', 'kategori' => 'Master', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Aksesoris', 'deskripsi' => 'Produk aksesoris', 'kategori' => 'Master', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Lainnya', 'deskripsi' => 'Produk lainnya', 'kategori' => 'Master', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Tradisional', 'deskripsi' => 'Produk jual satuan', 'kategori' => 'Jual', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Gift', 'deskripsi' =>'Produk jual beberapa', 'kategori' => 'Jual', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
