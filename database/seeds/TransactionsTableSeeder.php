<?php

use Illuminate\Database\Seeder;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->generateTestBets();
    }

    private function generateTestBets()
    {
        factory(App\Transaction::class, 1)->create()
        ->each(function ($transaction) {
            
            $txn_total_amount = 0;

            factory(App\Ticket::class)->create([
                'game_id' => $transaction->game_id,
                'area_id' => $transaction->user->agentAreas()->first()->id
            ])
            ->each(function ($ticket) use ($txn_total_amount) {

                $total_amount = 0;

                factory(App\Bet::class, 2)->create([
                    'transaction_id' => $ticket->transaction->id,
                    'ticket_id' => $ticket->id,
                ])->each(function($bet) use ($total_amount){
                    $total_amount += $bet->amount;
                });

                $ticket->total_amount = $total_amount;
                $ticket->save();

                $txn_total_amount += $ticket->total_amount;

            });

            $transaction->total_amount = $txn_total_amount;

        });
    }

}
