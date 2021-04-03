<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Ticket;
use Faker\Generator as Faker;
use GeneratorHelper as Generator;
use App\Transaction;
use App\User;

$factory->define(Ticket::class, function (Faker $faker) {

    $transactions = Transaction::all();
    $users = User::where('role', 'usher')->get();
    $draw_times = ['11', '16', '21'];

    return [
        'ticket_number' => Generator::generateTxnCode(),
        // 'draw_date' => $faker->dateTimeBetween('-7 days', 'now'),
        'draw_date' => $faker->dateTimeBetween('now', 'now'),
        'draw_time' => $draw_times[rand(0, 2)],
        'transaction_id' => $transactions->random()->id,
        'game_id' => $transactions->random()->game_id,
        'area_id' => $users->random()->agentAreas()->first()->id,
    ];
});
