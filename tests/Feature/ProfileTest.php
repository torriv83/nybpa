<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

use Spatie\Permission\Models\Role;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    Role::firstOrCreate(['name' => 'Admin']);
    $user->assignRole('Admin');

    $response = $this
        ->actingAs($user)
        ->get('/admin/profile');

    $response->assertOk();
});
