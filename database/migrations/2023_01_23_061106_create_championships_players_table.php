<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChampionshipsPlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('championship_players', function (Blueprint $table) {
            $table->id();
            $table->integer('id_championship');
            $table->integer('id_player');
            $table->float('points')->default(0);
            $table->integer('defeats')->default(0);
            $table->float('ppm')->default(1);
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
        Schema::dropIfExists('championship_players');
    }
}
