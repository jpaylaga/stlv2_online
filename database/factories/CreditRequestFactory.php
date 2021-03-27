<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\User;

$factory->define(App\CreditRequest::class, function (Faker $faker) {
    $type = $faker->randomElement(['deposit','withdraw']);
    $outlet = $faker->randomElement(['gcash','ml','cebuana']);
    $players = User::whereIn('role',['player','usher'])->pluck('id');
    $agents = User::where('role','coordinator')->pluck('id');

    return [
        'type' => $type,
        'status' => $faker->randomElement(['pending','cancelled','done']),
        'ref' => $faker->bothify('##??###??##?#'),
        'outlet' => $outlet,
        'amount' => $faker->randomElement([200,500,600,1000,2000,1500,2500,5000,10000]),
        'player_id' => $players->random(),
        'user_id' => $agents->random(),
    ];
});
