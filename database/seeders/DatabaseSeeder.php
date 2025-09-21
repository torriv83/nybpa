<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create roles if they don't exist
        $roles = ['Admin', 'Assistent', 'Fast ansatt', 'Tilkalling'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $data = [
            [
                'id' => '6',
                'name' => 'Tor J. Rivera',
                'email' => 'tor@trivera.net',
                'phone' => '47311906',
                'adresse' => 'Tollbugata 6',
                'postnummer' => '1776',
                'poststed' => 'Halden',
                'password' => Hash::make('voit63983'),
                'email_verified_at' => today(),
            ],
            [
                'id' => '4',
                'name' => 'Henrik Dahl',
                'email' => 'henrik.dahl.98@hotmail.com',
                'phone' => null,
                'adresse' => null,
                'postnummer' => null,
                'poststed' => null,
                'email_verified_at' => today(),
                'password' => Hash::make('henrik123'),
            ],
            [
                'id' => '8',
                'name' => 'Benjamin Huseby',
                'email' => fake()->unique()->safeEmail(),
                'phone' => null,
                'adresse' => null,
                'postnummer' => null,
                'poststed' => null,
                'email_verified_at' => today(),
                'password' => Hash::make('benjamin123'),
            ],
            [
                'id' => '1',
                'name' => 'Veronica Sudnell Hansen',
                'email' => fake()->unique()->safeEmail(),
                'phone' => null,
                'adresse' => null,
                'postnummer' => null,
                'poststed' => null,
                'email_verified_at' => today(),
                'password' => Hash::make('voit63983'),
            ],
            [
                'id' => '2',
                'name' => 'Cecilie Andersson',
                'email' => fake()->unique()->safeEmail(),
                'phone' => null,
                'adresse' => null,
                'postnummer' => null,
                'poststed' => null,
                'email_verified_at' => today(),
                'password' => Hash::make('voit63983'),
            ],
            [
                'id' => '3',
                'name' => 'Abelone Ledin',
                'email' => fake()->unique()->safeEmail(),
                'phone' => null,
                'adresse' => null,
                'postnummer' => null,
                'poststed' => null,
                'email_verified_at' => today(),
                'password' => Hash::make('voit63983'),
            ],
            [
                'id' => '5',
                'name' => 'Amalie Grimsrud',
                'email' => fake()->unique()->safeEmail(),
                'phone' => null,
                'adresse' => null,
                'postnummer' => null,
                'poststed' => null,
                'email_verified_at' => today(),
                'password' => Hash::make('voit63983'),
            ],
            [
                'id' => '9',
                'name' => 'Marius Moe Olsen',
                'email' => fake()->unique()->safeEmail(),
                'phone' => null,
                'adresse' => null,
                'postnummer' => null,
                'poststed' => null,
                'email_verified_at' => today(),
                'password' => Hash::make('voit63983'),
            ],
            // Add other data records here
        ];

        foreach ($data as $record) {
            $existingRecord = DB::table('users')
                ->where('id', $record['id'])
                ->first();

            if ($existingRecord) {
                $this->command->info('Data already exists for ID: '.$record['id'].'. Skipping seeder.');

                continue;
            }

            DB::table('users')->insert($record);
        }

        DB::table('settings')->insert(['user_id' => '6', 'weekplan_timespan' => '0', 'bpa_hours_per_week' => 7]);

        // Assign roles to users
        $this->assignRolesToUsers();
    }

    private function assignRolesToUsers()
    {
        // Get roles
        $adminRole = Role::where('name', 'Admin')->first();
        $assistentRole = Role::where('name', 'Assistent')->first();
        $fastAnsattRole = Role::where('name', 'Fast ansatt')->first();
        $tilkallingRole = Role::where('name', 'Tilkalling')->first();

        // Assign Admin role ONLY to tor@trivera.net
        $torUser = User::where('email', 'tor@trivera.net')->first();
        if ($torUser && $adminRole) {
            $torUser->assignRole($adminRole);
        }

        // Assign different roles to other users
        $otherRoles = [$assistentRole, $fastAnsattRole, $tilkallingRole];
        $otherUsers = User::where('email', '!=', 'tor@trivera.net')->get();

        foreach ($otherUsers as $index => $user) {
            $roleIndex = $index % count($otherRoles);
            $role = $otherRoles[$roleIndex];
            if ($role) {
                $user->assignRole($role);
            }
        }
    }
}
