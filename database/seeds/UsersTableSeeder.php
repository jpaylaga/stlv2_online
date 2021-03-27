<?php

use Illuminate\Database\Seeder;
use App\User;
use Carbon\Carbon;
use App\Outlet;
use App\Area;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // outlet
        $outlet = Outlet::all()->first();

        // area
        $area = Area::all()->first();

        User::create([
            'username' => 'admin',
            'role' => 'super_admin',
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'email' => 'admin@pbcoins.com',
            'phone' => '123123123',
            'password' => bcrypt('password'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        // foreach ($data as $code => $value) {
        //     for ($i=1; $i <= 3; $i++) {
        //         //create coordinator
        //         $coor_username = 'coor_' . $code . '0' . $i;
        //         $coordinator = User::create([
        //             'username' => $coor_username,
        //             'role' => 'coordinator',
        //             'firstname' => strtoupper($code),
        //             'lastname' => 'Coordinator 0' . $i,
        //             'email' => $coor_username . '@xcoins.com',
        //             'phone' => '123123123',
        //             'password' => bcrypt('password'),
        //         ]);
        //         $coordinator->areas()->attach( $value['area'] );

        //         // create the usher 
        //         $usher_username = 'usher_' . $code . '0' . $i;
        //         $usher = User::create([
        //             'username' => $usher_username,
        //             'role' => 'usher',
        //             'firstname' => strtoupper($code),
        //             'lastname' => 'Usher 0' . $i,
        //             'email' => $usher_username . '@xcoins.com',
        //             'phone' => '123123123',
        //             'password' => bcrypt('password'),
        //         ]);
        //         $coordinator->children()->attach( $usher->id );
        //         // $usher->outlets()->attach($outlet);
        //         $usher->agentAreas()->attach( $value['area'] );

        //     }
        // }

    }
}
