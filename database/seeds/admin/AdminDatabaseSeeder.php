<?php

use Illuminate\Database\Seeder;

class AdminDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminRoles::class);
        $this->call(AdminStatuses::class);
        $this->call(AdminUsers::class);
    }
}
