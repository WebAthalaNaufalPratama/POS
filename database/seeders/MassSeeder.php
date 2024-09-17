<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $produks = [];

        for ($i = 1; $i <= 100; $i++) {
            $produks[] = [
                'kode' => 'PRD-' . Str::random(5),
                'nama' => 'Produk ' . $i,
                'tipe_produk' => rand(1, 3), // Misalnya tipe produk antara 1 hingga 3
                'deskripsi' => 'Deskripsi untuk produk ' . $i,
                'satuan' => 'Unit', // Misalnya satuan untuk setiap produk
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('produks')->insert($produks);
    }
}
