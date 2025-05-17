<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ✅ Test 1
it('can render page login page', function () {
    auth()->logout();

    $this
        ->get('/admin/login')
        ->assertSuccessful();
})->group('feature');

// ✅ Test 2
test('users can authenticate using the login screen', function () {
    auth()->logout();

    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    Livewire::test(\Filament\Pages\Auth\Login::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasNoErrors();
});

// ✅ Test 3
test('users can not authenticate with invalid password', function () {
    auth()->logout();

    $user = User::factory()->create();

    $this->post('/login', [
        'email'    => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
