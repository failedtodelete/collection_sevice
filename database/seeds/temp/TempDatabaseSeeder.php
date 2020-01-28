<?php

use Illuminate\Database\Seeder;

class TempDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TempTypes::class);
        $this->call(TempLanguages::class);
        $this->call(TempAvailableLinks::class);
    }
}
