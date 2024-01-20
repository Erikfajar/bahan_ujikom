<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(5)->create();

        User::create([
            'name' => 'erik',
            'email' => 'erikfk1305@gmail.com',
            'password' => bcrypt('12345'),
            'isrole' => 1,
            'namerole' => 'administrator'
        ]);
    }
}
