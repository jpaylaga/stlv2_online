<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChildUserTable extends Migration
{
    /**
     * Run the migrations.
     * ref: https://laracasts.com/discuss/channels/laravel/user-to-user-relationship
     *
     * @return void
     */
    public function up()
    {
        Schema::create('child_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('child_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('child_id')->references('id')->on('users');
            $table->primary(['user_id', 'child_id']);
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
        Schema::dropIfExists('child_user');
    }
}
