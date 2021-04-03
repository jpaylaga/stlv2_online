<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Game;
use App\Price;
use Carbon\Carbon;
use App\Area;

class PricesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $admin = User::where('username', '=', 'admin')->get()->first();
        // $area = $pagadian_admin->areas()->allRelatedIds()->first();

        $areas = Area::all();
        $games = Game::all();
        foreach ($areas as $area) {
            foreach ($games as $game) {
                Price::create([
                    'price_per_unit' => 650,
                    'bet_limit' => 15,
                    'user_id' => $admin->id,
                    'area_id' => $area->id,
                    'game_id' => $game->id,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);
            }
        }

    }
}
