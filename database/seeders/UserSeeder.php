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
            'email'=>'a.shadanfar.it@gmail.com',
            'password'=> bcrypt('123!@#asd')
        ]);
    }
}
