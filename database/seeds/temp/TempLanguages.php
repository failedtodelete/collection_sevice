<?php

use Illuminate\Database\Seeder;

class TempLanguages extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Temp\Language::class, 2)->create();
    }
}
