<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(RefreshDatabase::class);
uses(Tests\TestCase::class)->in(__DIR__);

// ✅ Test 1: Login-siden rendres riktig
it('can render page login page', function () {
    auth()->logout();

    $this
        ->get('/admin/login')
        ->assertSuccessful();
})->group('feature');

// ✅ Test 2: Brukere kan autentisere seg
test('users can authenticate using the login screen', function () {
    auth()->logout();

    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    Livewire::test(\Filament\Pages\Auth\Login::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasNoErrors(); // Bytt til assertSuccessful() om nødvendig
});

// ✅ Test 3: Feil passord hindrer innlogging
test('users can not authenticate with invalid password', function () {
    auth()->logout();

    $user = User::factory()->create();

    $this->post('/login', [
        'email'    => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
