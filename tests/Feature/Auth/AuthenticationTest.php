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

    livewire::test('Filament\Pages\Auth\Login')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('authenticate')
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
