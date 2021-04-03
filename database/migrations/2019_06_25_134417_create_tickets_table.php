<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ticket_number', 128);
            $table->date('draw_date');
            $table->string('draw_time', 4);
            $table->decimal('total_amount', 8, 2)->default(0);
            $table->tinyInteger('print_count')->default(0);
            $table->string('customer_name', 128)->nullable();
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_checked')->default(false);
            $table->unsignedInteger('transaction_id');
            $table->unsignedInteger('game_id');
            $table->unsignedInteger('area_id');
            $table->timestamps();

            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreign('game_id')
                ->references('id')
                ->on('games')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreign('area_id')
                ->references('id')
                ->on('areas')
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
        Schema::dropIfExists('tickets');
    }
}
