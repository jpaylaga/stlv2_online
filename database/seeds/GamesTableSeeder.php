<?php

use Illuminate\Database\Seeder;

class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('games')->insert([
            [
                'name' => 'Old Lucky Tres(Local Draw)',
                'type' => 'swertres',
                'is_enabled' => true
            ],
            [
                'name' => 'New Lucky Tres(National 3D Lotto Result)',
                'type' => 'swertres',
                'is_enabled' => true
            ],
            [
                'name' => 'Hulog Holen',
                'type' => 'swertres',
                'is_enabled' => true
            ],
            // [
            //     'name' => 'Pares National',
            //     'type' => 'pares',
            //     'is_enabled' => false
            // ],
            // [
            //     'name' => 'Pares Local',
            //     'type' => 'pares',
            //     'is_enabled' => false
            // ],
        ]); 
    }
}
