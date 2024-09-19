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
        $ongkirs = [];

        for ($i = 1; $i <= 100; $i++) {
            $ongkirs[] = [
                'nama' => 'Produk ' . $i, // Value for 'nama'
                'lokasi_id' => rand(1, 3), // Random value between 1 and 3 for 'lokasi_id'
                'biaya' => rand(10000, 500000),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('ongkirs')->insert($ongkirs);
    }
}
