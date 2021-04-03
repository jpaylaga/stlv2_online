<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->increments('id');
            $table->double('price_per_unit', 8, 2);
            $table->tinyInteger('bet_limit');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('area_id');
            $table->unsignedInteger('game_id');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreign('area_id')
                ->references('id')
                ->on('areas')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreign('game_id')
                ->references('id')
                ->on('games')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }
}
