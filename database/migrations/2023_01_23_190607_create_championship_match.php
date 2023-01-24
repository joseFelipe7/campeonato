<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChampionshipMatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('championship_matchs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_championship');
            $table->integer('id_player_a');
            $table->integer('id_player_b');
            $table->integer('id_player_win')->nullable();
            $table->integer('group');
            $table->integer('round');
            $table->float('points')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('championship_matchs');
    }
}
