<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can render page', function () {
    /*    // Create the role (if it doesn't already exist)
        $role = Role::create(['name' => 'Admin']);

        // Create the user
        $user = User::factory()->create([
            'email'             => fake()->name . '@trivera.net',
            'email_verified_at' => now(),
        ]);

        // Assign the role to the user
        $user->assignRole('Admin');

        $this->actingAs($user);*/

    $this->get('admin/profile/')->assertSuccessful();
});

/*test('password can be updated', function () {

    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    // Set the currently logged in user for the application
    $this->actingAs($user);

    // Call the method responsible for updating the password
    livewire(Profile::class)
        ->fillForm(['name' => 'name'])
        ->fillForm(['email' => 'email'])
        ->fillForm(['phone' => '12345678'])
        ->fillForm(['current_password' => 'old-password'])
        ->fillForm(['new_password' => 'new-password'])
        ->fillForm(['new_password_confirmation' => 'new-password'])
        ->call('submit')
        ->assertHasNoFormErrors();
});*/

/*
test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password'      => 'wrong-password',
            'password'              => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'current_password')
        ->assertRedirect('/profile');
});*/
