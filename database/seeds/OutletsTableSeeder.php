<?php

use Illuminate\Database\Seeder;
use App\Outlet;
use Carbon\Carbon;

class OutletsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Outlet::insert([
            [
                'name' => 'Sample Outlet',
                'street' => 'Zone 999 Sample Street',
                'barangay' => 'Brgy Dos',
                'city' => 'Sample',
                'zipcode' => '12345',
                'province' => 'Lanao del Norte',
                'area_id' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ]);
       
    }
}
