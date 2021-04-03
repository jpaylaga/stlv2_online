<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\User;
use App\Area;

class AreasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $misor = Area::create([
            'name' => 'Misamis Oriental',
            'address' => 'Cagayan de Oro City',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $admin_misor = User::create([
            'username' => 'admin_misor',
            'role' => 'area_admin',
            'firstname' => 'MisOr',
            'lastname' => 'Admin',
            'email' => 'admin_misor@pbcoins.com',
            'phone' => '123123123',
            'password' => bcrypt('password'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $admin_misor->areas()->attach($misor);

        $pagadian = Area::create([
            'name' => 'Pagadian',
            'address' => 'Pagadian City',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $admin_pgdn = User::create([
            'username' => 'admin_pagadian',
            'role' => 'area_admin',
            'firstname' => 'MisOr',
            'lastname' => 'Admin',
            'email' => 'admin_pagadian@pbcoins.com',
            'phone' => '123123123',
            'password' => bcrypt('password'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $admin_pgdn->areas()->attach($pagadian);

    }
}
