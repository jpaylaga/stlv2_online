<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Bet;
use Faker\Generator as Faker;
use BetHelper as BetHelper;
use App\Ticket;
use App\Price;

$factory->define(Bet::class, function (Faker $faker) {

    $bet_type = $faker->randomElement( array('straight', 'ramble') );
    $ticket = Ticket::all()->random();
    $area_id = $ticket->transaction->user->agentAreas()->first()->id;
    $area_price = Price::select('price_per_unit')
        ->where([
            'area_id' => $area_id,
            'game_id' => $ticket->transaction->game->id,
        ])->first();
    
    $bet_amount = $bet_type == 'straight' ? $faker->numberBetween(0,15) : $faker->randomElement( array(6,12,18,24,30) );
    $combination = $faker->numerify('###');

    $bet_data['type'] = $bet_type;
    $bet_data['combination'] = $combination;

    return [
        'combination' => $faker->numerify('###'),
        'type' => $bet_type,
        'amount' => $bet_amount,
        'winning_amount' => BetHelper::getWinningAmount( $bet_data, $bet_amount, $area_price->price_per_unit, $area_id ),
        'transaction_id' => $ticket->transaction->id,
        'ticket_id' => $ticket->id,
    ];
});
