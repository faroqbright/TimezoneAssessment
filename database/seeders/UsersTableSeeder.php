<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $index) {
            User::insert([
                'name' => $faker->name,
                'email' => $faker->email,
                'timezone' => $faker->timezone,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
