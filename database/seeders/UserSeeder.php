<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create multiple users
        $user = [
            [
                'name' => 'fuad',
                'phone' => '085641173515',
                'email' => 'fuadagussalim@mail.ugm.ac.id',
                'password' => bcrypt('12345'),
            ],
         
        ];

        //insert the user into the database
        DB::table('users')->insert($user);

    }

}
