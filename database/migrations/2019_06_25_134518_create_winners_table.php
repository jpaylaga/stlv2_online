<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('winners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ticket_number', 128);
            $table->string('status', 64)->default('pending');
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('bet_id');
            $table->unsignedInteger('draw_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();

            $table->foreign('ticket_id')
                ->references('id')
                ->on('tickets')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreign('bet_id')
                ->references('id')
                ->on('bets')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreign('draw_id')
                ->references('id')
                ->on('draws')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('winners');
    }
}
