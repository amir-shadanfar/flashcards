<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name'=>'Amir Shadanfar',
            'email'=>'amir@gmail.com',
            'password'=> bcrypt('123')
        ]);

        User::factory()->create([
            'name'=>'Studocu',
            'email'=>'studocu@gmail.com',
            'password'=> bcrypt('123')
        ]);
    }
}
