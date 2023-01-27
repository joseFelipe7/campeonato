<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 10; $i < 20; $i++){
            DB::table('Players')->insert([
                'name' => 'Player '.($i+1).' '.Str::random(6),
                'email' => Str::random(10).'@email.com',
                'password' => Hash::make('123'),
            ]);
        }
    }
}
