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

        $data = [
            [
                'id'                => '6',
                'name'              => 'Tor J. Rivera',
                'email'             => 'tor@trivera.net',
                'phone'             => '47311906',
                'adresse'           => 'Tollbugata 6',
                'postnummer'        => '1776',
                'poststed'          => 'Halden',
                'password'          => Hash::make('voit63983'),
                'email_verified_at' => today(),
            ],
            [
                'id'                => '4',
                'name'              => 'Henrik Dahl',
                'email'             => 'henrik.dahl.98@hotmail.com',
                'phone'             => null,
                'adresse'           => null,
                'postnummer'        => null,
                'poststed'          => null,
                'email_verified_at' => today(),
                'password'          => Hash::make('henrik123'),
            ],
            [
                'id'                => '8',
                'name'              => 'Benjamin Huseby',
                'email'             => fake()->unique()->safeEmail(),
                'phone'             => null,
                'adresse'           => null,
                'postnummer'        => null,
                'poststed'          => null,
                'email_verified_at' => today(),
                'password'          => Hash::make('benjamin123'),
            ],
            [
                'id'                => '1',
                'name'              => 'Veronica Sudnell Hansen',
                'email'             => fake()->unique()->safeEmail(),
                'phone'             => null,
                'adresse'           => null,
                'postnummer'        => null,
                'poststed'          => null,
                'email_verified_at' => today(),
                'password'          => Hash::make('voit63983'),
            ],
            [
                'id'                => '2',
                'name'              => 'Cecilie Andersson',
                'email'             => fake()->unique()->safeEmail(),
                'phone'             => null,
                'adresse'           => null,
                'postnummer'        => null,
                'poststed'          => null,
                'email_verified_at' => today(),
                'password'          => Hash::make('voit63983'),
            ],
            [
                'id'                => '3',
                'name'              => 'Abelone Ledin',
                'email'             => fake()->unique()->safeEmail(),
                'phone'             => null,
                'adresse'           => null,
                'postnummer'        => null,
                'poststed'          => null,
                'email_verified_at' => today(),
                'password'          => Hash::make('voit63983'),
            ],
            [
                'id'                => '5',
                'name'              => 'Amalie Grimsrud',
                'email'             => fake()->unique()->safeEmail(),
                'phone'             => null,
                'adresse'           => null,
                'postnummer'        => null,
                'poststed'          => null,
                'email_verified_at' => today(),
                'password'          => Hash::make('voit63983'),
            ],
            [
                'id'                => '9',
                'name'              => 'Marius Moe Olsen',
                'email'             => fake()->unique()->safeEmail(),
                'phone'             => null,
                'adresse'           => null,
                'postnummer'        => null,
                'poststed'          => null,
                'email_verified_at' => today(),
                'password'          => Hash::make('voit63983'),
            ],
            // Add other data records here
        ];

        foreach ($data as $record) {
            $existingRecord = DB::table('users')
                ->where('id', $record['id'])
                ->first();

            if ($existingRecord) {
                $this->command->info('Data already exists for ID: ' . $record['id'] . '. Skipping seeder.');

                continue;
            }

            DB::table('users')->insert($record);
        }

        DB::table('settings')->insert(['user_id' => '6', 'weekplan_timespan' => '0', 'bpa_hours_per_week' => 7]);
    }
}
/*DB::table('users')->insert([
            [
                'id'                => '6',
                'name'              => 'Tor J. Rivera',
                'email'             => 'tor@trivera.net',
                'phone'             => '47311906',
                'adresse'           => 'Tollbugata 6',
                'postnummer'        => '1776',
                'poststed'          => 'Halden',
                'password'          => Hash::make('voit63983'),
                'email_verified_at' => today(),
            ],
            [
                'id'                => '4',
                'name'              => 'Henrik Dahl',
                'email'             => 'henrik.dahl.98@hotmail.com',
                'phone'             => '',
                'adresse'           => '',
                'postnummer'        => '',
                'poststed'          => '',
                'email_verified_at' => today(),
                'password'          => Hash::make('henrik123'),
            ],
            [
                'id'                => '8',
                'name'              => 'Benjamin Huseby',
                'email'             => 'test@test.com',
                'phone'             => '',
                'adresse'           => '',
                'postnummer'        => '',
                'poststed'          => '',
                'email_verified_at' => today(),
                'password'          => Hash::make('benjamin123'),
            ],
            [
                'id'                => '1',
                'name'              => 'Veronica Sudnell Hansen',
                'email'             => 'test@test.com',
                'phone'             => '',
                'adresse'           => '',
                'postnummer'        => '',
                'poststed'          => '',
                'email_verified_at' => today(),
                'password'          => Hash::make('voit63983'),
            ],
            [
                'id'                => '2',
                'name'              => 'Cecilie Andersson',
                'email'             => 'test@test.com',
                'phone'             => '',
                'adresse'           => '',
                'postnummer'        => '',
                'poststed'          => '',
                'email_verified_at' => today(),
                'password'          => Hash::make('voit63983'),
            ],
            [
                'id'                => '3',
                'name'              => 'Abelone Ledin',
                'email'             => 'test@test.com',
                'phone'             => '',
                'adresse'           => '',
                'postnummer'        => '',
                'poststed'          => '',
                'email_verified_at' => today(),
                'password'          => Hash::make('voit63983'),
            ],
            [
                'id'                => '5',
                'name'              => 'Amalie Grimsrud',
                'email'             => 'test@test.com',
                'phone'             => '',
                'adresse'           => '',
                'postnummer'        => '',
                'poststed'          => '',
                'email_verified_at' => today(),
                'password'          => Hash::make('voit63983'),
            ],
            [
                'id'                => '8',
                'name'              => 'Marius Moe Olsen',
                'email'             => 'test@test.com',
                'phone'             => '',
                'adresse'           => '',
                'postnummer'        => '',
                'poststed'          => '',
                'email_verified_at' => today(),
                'password'          => Hash::make('voit63983'),
            ]
        ]);*/
