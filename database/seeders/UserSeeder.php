<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                "name" => "Faris Hasyim",
                "email" => "faris.hasyim.03@gmail.com",
                "password" => Hash::make("11223344"),
                "role" => "admin",
                "gender" => "laki-laki",
                "phone" => "081223896063",
            ],
            [
                "name" => "Faris Hasyim",
                "email" => "fariscina22@gmail.com",
                "password" => Hash::make("11223344"),
                "role" => "customer",
                "gender" => "laki-laki",
                "phone" => "087824218035",
            ],
        ];
        User::insert($users);
    }
}
