<?php

use Illuminate\Database\Seeder;
use App\CreditRequest;

class CreditRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(CreditRequest::class, 10)->create();
    }
}
