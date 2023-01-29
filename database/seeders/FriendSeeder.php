<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FriendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('friends')->insert([
            [
                'id_player_send' => 1,
                'id_player_recived' => 2,
                'accept' => 0,
            ],[
                'id_player_send' => 1,
                'id_player_recived' => 3,
                'accept' => 1,
            ],[
                'id_player_send' => 1,
                'id_player_recived' => 4,
                'accept' => 1,
            ],[
                'id_player_send' => 1,
                'id_player_recived' => 5,
                'accept' => 0,
            ],[
                'id_player_send' => 6,
                'id_player_recived' => 1,
                'accept' => 0,
            ],[
                'id_player_send' => 7,
                'id_player_recived' => 1,
                'accept' => 0,
            ],[
                'id_player_send' => 8,
                'id_player_recived' => 1,
                'accept' => 1,
            ],[
                'id_player_send' => 9,
                'id_player_recived' => 1,
                'accept' => 1,
            ],[
                'id_player_send' => 1,
                'id_player_recived' => 12,
                'accept' => 0,
            ],[
                'id_player_send' => 1,
                'id_player_recived' => 13,
                'accept' => 1,
            ],[
                'id_player_send' => 1,
                'id_player_recived' => 14,
                'accept' => 1,
            ],[
                'id_player_send' => 1,
                'id_player_recived' => 15,
                'accept' => 0,
            ],[
                'id_player_send' => 16,
                'id_player_recived' => 1,
                'accept' => 0,
            ],[
                'id_player_send' => 17,
                'id_player_recived' => 1,
                'accept' => 0,
            ],[
                'id_player_send' => 18,
                'id_player_recived' => 1,
                'accept' => 1,
            ],[
                'id_player_send' => 19,
                'id_player_recived' => 1,
                'accept' => 1,
            ]
        ]);

    }
}
