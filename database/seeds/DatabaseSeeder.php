<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        factory(SET\User::class, 50)->create();
        factory(SET\Training::class, 15)->create();
        factory(SET\Note::class, 200)->create();

        Model::reguard();
    }
}
