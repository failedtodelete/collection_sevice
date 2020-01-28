<?php

use Illuminate\Database\Seeder;

class PublicDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PublicSites::class);
    }
}
