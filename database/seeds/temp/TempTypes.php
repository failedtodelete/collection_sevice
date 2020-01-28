<?php

use Illuminate\Database\Seeder;

class TempTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Temp\Type::class, 3)->create();
    }
}
