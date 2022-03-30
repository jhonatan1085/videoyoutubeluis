<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'=>'Alejandro',
            'email'=>'jhonatan10_85@hotmail.com',
            'password'=>bcrypt('123'),
            'profile'=>'ADMIN',
            'phone'=>'990217797',
            'status'=>'ACTIVE',
            'image'=>'user.png'
        ]);
        User::create([
            'name'=>'Liz',
            'email'=>'Liz_83@hotmail.com',
            'password'=>bcrypt('123'),
            'profile'=>'EMPLOYEE',
            'phone'=>'990326598',
            'status'=>'ACTIVE',
            'image'=>'user.png'
        ]);
    }
}
