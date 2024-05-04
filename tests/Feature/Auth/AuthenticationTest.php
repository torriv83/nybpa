<?php

use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can render page login page', function () {
    auth()->logout();
    $this->get('/admin/login')->assertSuccessful();
});

test('users can authenticate using the login screen', function () {
    auth()->logout();
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    Livewire::test('Filament\Pages\Auth\Login')
        ->set('data.email', $user->email) // Update to set the email field in the form
        ->set('data.password', 'password') // Set the password field
        ->call('authenticate') // Call the authenticate method
        ->assertSuccessful();
});

test('users can not authenticate with invalid password', function () {
    auth()->logout();
    $user = User::factory()->create();

    $this->post('/login', [
        'email'    => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
