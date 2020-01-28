<?php

use Illuminate\Database\Seeder;

class TempAvailableLinks extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Temp\Link::class, 100)->create([
            'status' => \App\Models\Temp\Link::CONFIRMED
        ]);
    }
}
