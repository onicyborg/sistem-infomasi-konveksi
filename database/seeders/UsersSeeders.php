<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('passwordadmin'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kepala Produksi',
                'username' => 'kepala_produksi',
                'password' => Hash::make('passwordkepalaproduksi'),
                'role' => 'kepala_produksi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
