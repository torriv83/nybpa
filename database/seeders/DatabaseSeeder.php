<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        DB::table('users')->insert([
            'id' => '1',
            'name' => 'Tor J. Rivera',
            'email' => 'tor@trivera.net',
            'phone' => '47311906',
            'adresse' => 'Tollbugata 6',
            'postnummer' => '1776',
            'poststed' => 'Halden',
            'password' => Hash::make('voit63983'),
        ],
        [
            'id' => '4',
            'name' => 'Henrik Dahl',
            'email' => 'henrik.dahl.98@hotmail.com',
            'password' => Hash::make('henrik123'),
        ],
        [
            'id' => '8',
            'name' => 'Benjamin Huseby',
            'email' => 'test@test.com',
            'password' => Hash::make('benjamin123'),
        ]);
    }
}
