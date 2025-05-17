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
        'password' => Hash::make('password'),
    ]);

    // Bruk Livewire for å teste login-skjemaet
    Livewire::test(\Filament\Pages\Auth\Login::class)
        ->set('data.email', $user->email)  // Sett e-post
        ->set('data.password', 'password')  // Sett passord
        ->call('authenticate')  // Kall autentisering
        ->assertHasNoErrors();  // Bekreft at det ikke er noen feil
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
