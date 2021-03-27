<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Transaction;
use Faker\Generator as Faker;
use GeneratorHelper as Generator;
use App\User;
use App\Game;

$factory->define(Transaction::class, function (Faker $faker) {
    $games = Game::where('type', 'swertres')->pluck('id');
    $users = User::where('role', 'usher')->pluck('id');
    return [
        'code' => Generator::generateTxnCode(),
        'game_id' => $games->random(),
        'user_id' => $users->random(),
    ];
});
